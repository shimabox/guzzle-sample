<?php

namespace Tests\Sample\Middleware;

use App\Sample\Middleware\RateLimitMiddleware;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

/**
 * このテストでは、RateLimitMiddlewareがレートリミットを超えた場合にsleep()で待機するか、
 * そうでない場合はすぐにリクエストを処理するかを検証します。
 */
class RateLimitMiddlewareTest extends TestCase
{
    public function test_レートリミットを超えなければ待機しない()
    {
        /*
         |---------------------
         | 準備
         |---------------------
         */
        // limit=3, interval=1.0で1秒間に3回まで
        $middleware = new class(3, 1.0) extends RateLimitMiddleware {
            private float $time = 100.0; // 現在時刻を100秒付近からスタート

            protected function getTime(): float
            {
                // このクラスではgetTime()で固定の$timeを返す
                return $this->time;
            }

            protected function sleep(int $microseconds): void
            {
                // sleepは実行されない
                throw new RuntimeException("sleep() が実行された");
            }


            public function advanceTime(float $sec): void
            {
                // テスト中に時間を経過させる
                $this->time += $sec;
            }
        };

        /*
         |---------------------
         | 実行
         |---------------------
         */
        $handler = function (RequestInterface $request, array $options): PromiseInterface {
            $this->assertTrue(true); // ハンドラーが呼ばれたことを確認
            // 適当なFulfilled Promiseを返す
            return $this->createAnonymousPromiseInterface();
        };

        // ミドルウェアを取得
        $callable = $middleware($handler);

        // Request Mock
        $request = $this->createMock(RequestInterface::class);

        /*
         |---------------------
         | 検証
         |---------------------
         */
        // 3回連続で呼んでもlimit=3以内ならsleepしない
        for ($i = 0; $i < 3; $i++) {
            $callable($request, []);
            $middleware->advanceTime(0.1); // 0.1秒進める
        }

        $this->assertTrue(true, "sleep() は実行されていない");
    }

    public function test_レートリミットを超えたら待機する()
    {
        /*
         |---------------------
         | 準備
         |---------------------
         */
        // limit=2, interval=1.0で1秒間に2回まで
        $middleware = new class(2, 1.0) extends RateLimitMiddleware {
            private float $time = 200.0;
            public int $sleepCalled = 0;
            public ?int $lastSleepTime = null;

            protected function getTime(): float
            {
                return $this->time;
            }

            protected function sleep(int $microseconds): void
            {
                // limit超過時はsleepが呼ばれるはず
                $this->sleepCalled++;
                $this->lastSleepTime = $microseconds;
            }

            public function advanceTime(float $sec): void
            {
                $this->time += $sec;
            }
        };

        /*
         |---------------------
         | 実行
         |---------------------
         */
        $handler = function (RequestInterface $request, array $options): PromiseInterface {
            return $this->createAnonymousPromiseInterface();
        };
        $callable = $middleware($handler);

        $request = $this->createMock(RequestInterface::class);

        /*
         |---------------------
         | 検証
         |---------------------
         */
        // 1回目のリクエスト（200.0s）
        $callable($request, []);
        $middleware->advanceTime(0.2); // 200.2s

        // 2回目のリクエスト（1秒以内に2回目なのでOK）
        $callable($request, []);
        $middleware->advanceTime(0.2); // 200.4s

        // ここで3回目をすぐ送るとlimit=2を超えるため待機するはず
        // interval=1秒以内に既に2回送っているので超過
        $callable($request, []);
        $this->assertSame(1, $middleware->sleepCalled);
        $this->assertNotNull($middleware->lastSleepTime);
        $this->assertGreaterThan(0, $middleware->lastSleepTime);
    }

    /**
     * PromiseInterfaceを実装して、Guzzleが期待する返り値を返す
     *
     * @return PromiseInterface
     */
    private function createAnonymousPromiseInterface(): PromiseInterface
    {
        return new class implements PromiseInterface {
            public function then(callable $onFulfilled = null, callable $onRejected = null): PromiseInterface
            {
                return $this;
            }

            public function otherwise(callable $onRejected): PromiseInterface
            {
                return $this;
            }

            public function wait($unwrap = true) {}

            public function getState(): string
            {
                return 'fulfilled';
            }

            public function resolve($value): void {}

            public function reject($reason): void {}

            public function cancel(): void {}
        };
    }
}
