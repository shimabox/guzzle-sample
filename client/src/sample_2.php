<?php

namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';

$client = new Client([
    'base_uri' => $baseUrl,
]);

$start  = hrtime(true);

$client->sendRequest(new Request('GET', '/sample_2/1'));
$client->sendRequest(new Request('GET', '/sample_2/12'));
$client->sendRequest(new Request('GET', '/sample_2/123'));

$total = (hrtime(true) - $start) / 1e+9;
echo "{$total} 秒" . PHP_EOL;
