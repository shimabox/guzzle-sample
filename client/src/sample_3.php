<?php

namespace App;

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;

$baseUrl = $_ENV['API_BASE_URL'] ?? 'http://api';
$client = new Client([
    'base_uri' => $baseUrl,
]);

$start  = hrtime(true);

// ここではリクエストは投げられていない
$requests = [
    // $client->getAsync('/sample_3/1')でもよいが、
    // GuzzleHttp\ClientInterfaceでrequestAsyncが定義されているので扱いやすい
    'req1' => $client->requestAsync('GET', '/sample_3/1'),
    'req2' => $client->requestAsync('GET', '/sample_3/12'),
    'req3' => $client->requestAsync('GET', '/sample_3/123'),
];

// Utils::settle()でGuzzleHttp\Promise\PromiseInterfaceが返る(Promiseってやつ)
// PromiseInterfaceのwait()を呼ばないと処理は実行されない(Promiseは解決されない)
$promises = Utils::settle($requests);
$results = $promises->wait();

// $resultsはPromiseが解決されているので後は好きにしてもろうて
foreach ($results as $key => $result) {
    if ($result['state'] === PromiseInterface::FULFILLED
    ) {
        echo "$key success" . PHP_EOL;
    } else { // こっちは、PromiseInterface::REJECTED
        echo "$key failed" . PHP_EOL;
    }
}

$total = (hrtime(true) - $start) / 1e+9;
echo "{$total} 秒" . PHP_EOL;
