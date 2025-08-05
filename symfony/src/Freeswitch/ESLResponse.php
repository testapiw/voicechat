<?php

namespace App\Freeswitch;

class ESLResponse
{
    private string $body;

    public function __construct(string $body)
    {
        $this->body = $body;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}