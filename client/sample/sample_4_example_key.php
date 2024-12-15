<?php

namespace Sample;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientExceptionInterface;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';
$client = new Client([
    'base_uri' => $baseUrl,
]);

$params = [ // 文字列が識別子となっているケース
    'a' => ['id' => 1], 'b' => ['id' => 2], 'c' => ['id' => 3], 'd' => ['id' => 4], 'e' => ['id' => 5], 'f' => ['id' => 6],
    'g' => ['id' => 7], 'h' => ['id' => 8], 'i' => ['id' => 9], 'j' => ['id' => 10], 'k' => ['id' => 11],
    'l' => ['id' => 12], 'm' => ['id' => 13], 'n' => ['id' => 14], 'o' => ['id' => 15], 'p' => ['id' => 16],
    'q' => ['id' => 17], 'r' => ['id' => 18], 's' => ['id' => 19], 't' => ['id' => 20], 'u' => ['id' => 21],
    'v' => ['id' => 22], 'w' => ['id' => 23], 'x' => ['id' => 24], 'y' => ['id' => 25], 'z' => ['id' => 26],
];

$requests = function ($params) use ($client) {
    foreach ($params as $key => $param) {
        // yield で $key を渡すことにより、コールバックで識別子が使える
        yield $key => fn() => $client->requestAsync('GET', '/sample_4/' . $param['id']);
    }
};

$pool = new Pool($client, $requests($params), [
    'concurrency' => 10,
    'fulfilled' => fn(Response $res, $key) => print("{$params[$key]['id']}\n"), // yieldで渡した$key
    'rejected' => fn(ClientExceptionInterface $reason, $key) => print("{$key} failed: {$reason}\n"),
]);

$pool->promise()->wait();
