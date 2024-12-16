<?php

namespace App\Sample;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Sample\Handler\FulfillHandler;
use App\Sample\Handler\RejectedHandler;
use App\Sample\Pool\ClientPoolFactory;
use GuzzleHttp\Client;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';
$client = new Client([
    'base_uri' => $baseUrl,
]);

$params = [
    'foo' => ['id' => 1, 'name' => 'foo'],
    'bar' => ['id' => 2, 'name' => 'bar'],
    'baz' => ['id' => 3, 'name' => 'baz'],
    'hoge' => ['id' => 4, 'name' => 'hoge'],
    'fuga' => ['id' => 5, 'name' => 'fuga'],
    'piyo' => ['id' => 6, 'name' => 'piyo'],
];

$guzzleSample = new GuzzleSample(
    $client,
    new ClientPoolFactory(),
    new FulfillHandler(),
    new RejectedHandler(),
    $params,
  3
);
$result = $guzzleSample->call();

echo json_encode($result->getResult());
