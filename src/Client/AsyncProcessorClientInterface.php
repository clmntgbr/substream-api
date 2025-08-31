<?php

namespace App\Client;

interface AsyncProcessorClientInterface
{
    public function request(string $method, string $url, array $options = []): void;
}