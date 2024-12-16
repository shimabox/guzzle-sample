<?php

namespace App\Sample;

require_once __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;

readonly class GuzzleSample
{
    public function __construct(
        private ClientInterface $client,
        private ClientPoolFactoryInterface $poolFactory,
        private FulfilledHandlerInterface $fulfilledHandler,
        private RejectedHandlerInterface $rejectedHandler,
        private array $params,
        private int $concurrency = 10
    ) {}

    public function call(): Result
    {
        $requests = function ($params) {
            foreach ($params as $key => $param) {
                yield $key => fn() => $this->client->requestAsync('GET', "/sample_test/{$param['id']}/{$param['name']}");
            }
        };

        $pool = $this->poolFactory->factory($this->client, $requests($this->params),
            [
                'concurrency' => $this->concurrency,
                'fulfilled' => fn(Response $res, $key) => $this->fulfilledHandler->handle($res, $key),
                'rejected' => fn(ClientExceptionInterface $reason, $key) => $this->rejectedHandler->handle($reason, $key),
            ]
        );
        $promise = $pool->promise();
        $promise->wait();

        return new Result($this->fulfilledHandler, $this->rejectedHandler);
    }
}
