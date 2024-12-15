<?php

namespace App\Sample\Handler;

use App\Sample\FulfilledHandlerInterface;
use GuzzleHttp\Psr7\Response;

class FulfillHandler implements FulfilledHandlerInterface
{
    public function handle(Response $res, array $reqParams): void
    {
        echo "FulfillHandler: {$res->getBody()}" . PHP_EOL;
    }
}