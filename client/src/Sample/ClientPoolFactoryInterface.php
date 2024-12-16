<?php

namespace App\Sample;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Pool;

interface ClientPoolFactoryInterface
{
    public function factory(
        ClientInterface $client,
        $requests,
        array $config = []
    ): Pool;
}
