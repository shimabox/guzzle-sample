<?php

namespace App\Sample;

use Psr\Http\Client\ClientExceptionInterface;

interface RejectedHandlerInterface
{
    public function handle(ClientExceptionInterface $e, array $reqParams): void;
}