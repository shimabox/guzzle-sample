<?php

namespace App\Sample\Pool;

use App\Sample\ClientPoolFactoryInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;

class ClientPoolFactory implements ClientPoolFactoryInterface
{
    public function factory(
        ClientInterface $client,
        $requests,
        array $config = []
    ): Pool {
        return new Pool($client, $requests, $config);
    }
}