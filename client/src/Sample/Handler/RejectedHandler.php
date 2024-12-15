<?php

namespace App\Sample\Handler;

use App\Sample\RejectedHandlerInterface;
use Psr\Http\Client\ClientExceptionInterface;

class RejectedHandler implements RejectedHandlerInterface
{
    public function handle(ClientExceptionInterface $e, array $reqParams): void
    {
        error_log(
            "RejectedHandler: {$e->getMessage()}",
            3,
            '/var/www/html/logs/error.log'
        );
        echo "RejectedHandler: {$e->getMessage()}" . PHP_EOL;
    }
}