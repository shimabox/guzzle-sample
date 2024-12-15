<?php

require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';

$client = new Client([
    'base_uri' => $baseUrl,
]);

$response = $client->request('GET', '/');
echo $response->getBody();
