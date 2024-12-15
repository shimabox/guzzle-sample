<?php

namespace Sample;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';

$client = new Client([
    'base_uri' => $baseUrl,
]);

$res = $client->sendRequest(new Request('GET', '/sample_1/123'));
echo $res->getBody();
