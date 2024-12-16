<?php

namespace App\Sample\Handler;

use App\Sample\FulfilledHandlerInterface;
use GuzzleHttp\Psr7\Response;

class FulfillHandler implements FulfilledHandlerInterface
{
    private array $result = [];

    public function handle(Response $res, string $key): void
    {
        $this->result[$key] = json_decode($res->getBody()->getContents());
    }

    public function getResult(): array
    {
        return $this->result;
    }
}
