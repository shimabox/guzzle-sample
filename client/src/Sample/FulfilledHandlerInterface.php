<?php

namespace App\Sample;

use GuzzleHttp\Psr7\Response;

interface FulfilledHandlerInterface
{
    public function handle(Response $res, string $key): void;

    public function getResult(): array;
}
