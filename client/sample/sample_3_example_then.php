<?php

namespace Sample;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';
$client = new Client([
    'base_uri' => $baseUrl,
]);

$start  = hrtime(true);

$requests = [
    // requestAsync() のいずれかの処理で失敗すると、他の処理は実行されない
    $client->requestAsync('GET', '/sample_3/1')->then(
        function (Response $response) { // 成功時の処理
            echo $response->getBody();
        },
        function (ClientExceptionInterface $reason) { // 失敗時の処理
            // https://docs.guzzlephp.org/en/latest/quickstart.html#exceptions
            // GuzzleHttp\Exception\ConnectException または、
            // GuzzleHttp\Exception\RequestException が実装している
            echo $reason;
        },
    ),
    $client->requestAsync('GET', '/sample_3/12')->then(
        function (Response $response) {
            echo $response->getBody();
        },
        function (ClientExceptionInterface $reason) {
            echo $reason;
        },
    ),
    $client->requestAsync('GET', '/sample_3/123')->then(
        function (Response $response) {
            echo $response->getBody();
        },
        function (ClientExceptionInterface $reason) {
            echo $reason;
        },
    ),
];
Utils::settle($requests)->wait();

$total = (hrtime(true) - $start) / 1e+9;
echo "{$total} 秒" . PHP_EOL;
