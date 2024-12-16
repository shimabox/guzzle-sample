<?php

namespace App\Sample;

use Psr\Http\Client\ClientExceptionInterface;

interface RejectedHandlerInterface
{
    public function handle(ClientExceptionInterface $e, string $key): void;

    public function getResult(): array;
}
