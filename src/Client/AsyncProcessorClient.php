<?php

namespace App\Client;

use App\Client\ProcessorClientInterface;
use App\Exception\ProcessorException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TimeoutExceptionInterface;

final class AsyncProcessorClient implements AsyncProcessorClientInterface
{
    public function __construct(
        private HttpClientInterface $processorClient,
        private LoggerInterface $logger
    ) {}

    public function request(string $method, string $url, array $options = []): void
    {
        try {
            $this->processorClient->request($method, $url, [
                'json' => $options['json'],
                'timeout' => 1,
                'max_duration' => 1,
            ]);
        } catch (ClientExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $exception) {
            throw new ProcessorException($exception->getMessage());
        }
    }
}