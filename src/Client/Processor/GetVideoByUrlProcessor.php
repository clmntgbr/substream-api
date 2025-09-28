<?php

namespace App\Client\Processor;

use App\Dto\GetVideoByUrl;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetVideoByUrlProcessor implements GetVideoByUrlProcessorInterface
{
    public const PROCESSOR_GET_VIDEO_BY_URL = '/api/download/video/url';

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
        try {
            $response = $this->processorClient->request('POST', self::PROCESSOR_GET_VIDEO_BY_URL, [
                'json' => $dto->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to get video by url');
        }
    }
}
