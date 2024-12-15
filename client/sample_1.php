<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$baseUrl = getenv('API_BASE_URL') ?: 'http://api';

$client = new Client([
    'base_uri' => $baseUrl,
]);

$response = $client->request('GET', '/');
echo $response->getBody();
