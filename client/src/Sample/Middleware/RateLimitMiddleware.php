<?php

namespace App\Sample\Middleware;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * レートリミット(スロットリング)を行うGuzzle用ミドルウェア
 *
 * 指定した秒数(interval)あたりのリクエスト回数(limit)を超えないようにするため、
 * 必要に応じて送信前に待機(usleep)を行う。
 *
 * たとえば、limit=3, interval=1.0 の場合、「1秒間に3回までリクエスト可能」という制限になる。
 *
 * @see https://docs.guzzlephp.org/en/latest/handlers-and-middleware.html
 */
class RateLimitMiddleware
{
    /** @var float[] 過去に送ったリクエストのタイムスタンプ(秒数)を保持 */
    private array $timestamps = [];

    /**
     * コンストラクタ
     *
     * @param int   $limit    interval秒間に送れるリクエスト回数の上限
     * @param float $interval リクエスト回数をカウントする間隔(秒)
     *
     * 例: $limit=3, $interval=1.0なら「1秒間に3回まで」
     */
    public function __construct(private int $limit, private float $interval) {}

    /**
     * ミドルウェア本体
     *
     * Guzzleのミドルウェアとして呼び出されるクロージャを返します。
     * リクエスト送信前に、直近interval秒以内のリクエスト回数をチェックし、limitを超えていればusleepで待機します。
     *
     * @param callable $handler Guzzleの次のハンドラ
     * @return callable
     */
    public function __invoke(callable $handler): callable
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {
            $now = microtime(true);

            // 現在時刻からinterval秒以上前のリクエストはカウント対象外なので除外
            $this->timestamps = array_filter($this->timestamps, fn($t) => $t > $now - $this->interval);

            // interval秒以内に送ったリクエストの数をチェックする
            // もしlimit回以上（例：4回以上）を既に送っているなら、すぐに新しいリクエストを送ると上限を超えてしまうので待機する
            if (count($this->timestamps) >= $this->limit) {
                // interval秒以内に送った中で、一番古いリクエストのタイムスタンプを取得する
                $oldestTimestamp = min($this->timestamps);

                // 一番古いリクエストのタイムスタンプから、次のリクエストを送るまでの待ち時間を計算
                $waitTime = ($oldestTimestamp + $this->interval) - $now;

                // $waitTimeが0より大きい場合は待機する
                if ($waitTime > 0) {
                    // $waitTime(秒)をマイクロ秒に変換して待機
                    // 0.2秒待ちたいなら0.2*1000000=200000マイクロ秒待つ(もうちょっと係数を増やしてもいいのかもしれない)
                    usleep((int)($waitTime * 1000000));
                }
            }

            // リクエスト時刻を記録
            $this->timestamps[] = microtime(true);

            return $handler($request, $options);
        };
    }
}
