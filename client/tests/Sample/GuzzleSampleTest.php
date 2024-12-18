<?php

namespace Tests\Sample;

use App\Sample\ClientPoolFactoryInterface;
use App\Sample\Handler\FulfillHandler;
use App\Sample\GuzzleSample;
use App\Sample\Handler\RejectedHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GuzzleSampleTest extends TestCase
{
    /**
     * @var array ClientPoolFactoryへの設定
     */
    private array $capturedClientPoolFactoryConfig = [];

    public function test_すべて成功()
    {
        /*
         |---------------------
         | 準備
         |---------------------
         */
        $params = [
            'foo' => ['id' => 1, 'name' => 'foo'],
            'bar' => ['id' => 2, 'name' => 'bar'],
        ];

        $queue = [
            new Response(200, [], json_encode($params['foo'])),
            new Response(200, [], json_encode($params['bar'])),
        ];

        // MockHandlerとHistoryMiddlewareでリクエスト履歴をとる
        $historyContainer = [];
        $clientMock = $this->createMockClient($queue, $historyContainer);

        // 期待する同時リクエスト数
        $expectedConcurrency = 2;

        $factoryMock = $this->createMock(ClientPoolFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('factory')
            ->willReturnCallback(function($client, $requests, $config) {
                // factory呼び出し時にClientPoolFactoryへの設定をキャプチャ
                $this->capturedClientPoolFactoryConfig = $config;
                return new Pool($client, $requests, $config);
            });

        /*
         |---------------------
         | 実行
         |---------------------
         */
        $sut = new GuzzleSample(
            client: $clientMock,
            poolFactory: $factoryMock,
            fulfilledHandler: new FulfillHandler(),
            rejectedHandler: new RejectedHandler(),
            params: $params,
            concurrency: $expectedConcurrency
        );
        $result = $sut->call()->getResult();
        $actual = json_decode($result, true);

        /*
         |---------------------
         | 検証
         |---------------------
         */
        $expectedBody = [
            'result' => [
                'foo' => $params['foo'],
                'bar' => $params['bar'],
            ],
            'error' => [],
        ];

        // レスポンスの中身が正しいか
        $this->assertSame($expectedBody, $actual);

        // 1回目のリクエストURI確認
        $this->assertSame('GET', $historyContainer[0]['request']->getMethod());
        $this->assertSame('/sample_test/1/foo', $historyContainer[0]['request']->getUri()->getPath());
        // 2回目のリクエストURI確認
        $this->assertSame('GET', $historyContainer[1]['request']->getMethod());
        $this->assertSame('/sample_test/2/bar', $historyContainer[1]['request']->getUri()->getPath());

        // factoryに渡されたconfig(concurrency)をチェック
        $this->assertSame($expectedConcurrency, $this->capturedClientPoolFactoryConfig['concurrency']);
    }

    public function test_2件中1件成功()
    {
        /*
         |---------------------
         | 準備
         |---------------------
         */
        $params = [
            'foo' => ['id' => 1, 'name' => 'foo'],
            'bar' => ['id' => 2, 'name' => 'bar'],
        ];

        $queue = [
            new Response(200, [], json_encode($params['foo'])),
            new Response(500, [], json_encode($params['bar'])),
        ];

        // MockHandlerとHistoryMiddlewareでリクエスト履歴をとる
        $historyContainer = [];
        $clientMock = $this->createMockClient($queue, $historyContainer);

        // 期待する同時リクエスト数
        $expectedConcurrency = 2;

        $factoryMock = $this->createMock(ClientPoolFactoryInterface::class);
        $factoryMock->expects($this->once())
            ->method('factory')
            ->willReturnCallback(function($client, $requests, $config) {
                // factory呼び出し時にClientPoolFactoryへの設定をキャプチャ
                $this->capturedClientPoolFactoryConfig = $config;
                return new Pool($client, $requests, $config);
            });

        /*
         |---------------------
         | 実行
         |---------------------
         */
        $sut = new GuzzleSample(
            client: $clientMock,
            poolFactory: $factoryMock,
            fulfilledHandler: new FulfillHandler(),
            rejectedHandler: new RejectedHandler(),
            params: $params,
            concurrency: $expectedConcurrency
        );
        $result = $sut->call()->getResult();
        $actual = json_decode($result, true);

        /*
         |---------------------
         | 検証
         |---------------------
         */
        $errorMessage = <<<EOF
Server error: `GET /sample_test/2/bar` resulted in a `500 Internal Server Error` response:
{"id":2,"name":"bar"}

EOF;
        $expectedBody = [
            'result' => [
                'foo' => $params['foo'],
            ],
            'error' => [
                'bar' => [
                    'error_code' => 500,
                    'error_message' => $errorMessage,
                ],
            ],
        ];

        // レスポンスの中身が正しいか
        $this->assertSame($expectedBody, $actual);

        // 1回目のリクエストURI確認
        $this->assertSame('GET', $historyContainer[0]['request']->getMethod());
        $this->assertSame('/sample_test/1/foo', $historyContainer[0]['request']->getUri()->getPath());
        // 2回目のリクエストURI確認
        $this->assertSame('GET', $historyContainer[1]['request']->getMethod());
        $this->assertSame('/sample_test/2/bar', $historyContainer[1]['request']->getUri()->getPath());

        // factoryに渡されたconfig(concurrency)をチェック
        $this->assertSame($expectedConcurrency, $this->capturedClientPoolFactoryConfig['concurrency']);
    }

    /**
     * 以下のClientを作成
     * - queue配列に入れたResponseやExceptionを順番に返す
     * - MockHandlerとHistory Middlewareでリクエスト履歴をとる
     *
     * @param array $queue
     * @param array $historyContainer
     * @return Client
     * @see https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     * @see https://docs.guzzlephp.org/en/stable/testing.html#history-middleware
     */
    private function createMockClient(array $queue, array &$historyContainer): Client
    {
        $mock = new MockHandler($queue);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push(Middleware::history($historyContainer));
        return new Client(['handler' => $handlerStack]);
    }
}
