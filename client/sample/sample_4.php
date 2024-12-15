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

// Poolから呼び出される
// リクエストをGeneratorで返す
$requests = function ($total) use ($client) {
    for ($i = 1; $i <= $total; $i++) {
        yield fn() => $client->requestAsync('GET', '/sample_4/' . $i);
    }
};

// Poolの生成
// 送信するリクエストを管理する
// (EachPromiseでPromiseを管理)
$pool = new Pool($client, $requests(100), [
    'concurrency' => 10, // 10個ずつリクエストを投げる(デフォルト25回)
    // このへんthen()に似てるね
    'fulfilled' => fn(Response $res, $i) => print("{$i} completed.\n"), // 成功時の処理
    'rejected' => fn(ClientExceptionInterface $reason, $i) => print("{$i} failed: {$reason}\n"), // 失敗時の処理
]);

$pool->promise()->wait();
