<?php

namespace App\Client\Processor;

use App\Client\AsyncProcessorClientInterface;
use App\Dto\Processor\GetVideoByUrl;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetVideoByUrlProcessor implements GetVideoByUrlProcessorInterface
{
    const GET_VIDEO_BY_URL_URL = '/api/download/video/url';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(GetVideoByUrl $dto): void
    {
        $response = $this->processorClient->request('POST', self::GET_VIDEO_BY_URL_URL, [
            'json' => $dto->jsonSerialize(),
            'headers' => [
                'Authorization' => $this->processorToken,
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            throw new ProcessorException('Failed to get video by url');
        }
    }
}