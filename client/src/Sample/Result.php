<?php

namespace App\Sample;

class Result
{
    public function __construct(
        private FulfilledHandlerInterface $fulfilledHandler,
        private RejectedHandlerInterface $rejectedHandler
    ) { }

    public function getResult(): string
    {
        return json_encode([
            'result' => $this->fulfilledHandler->getResult(),
            'error' => $this->rejectedHandler->getResult()
        ]);
    }
}
