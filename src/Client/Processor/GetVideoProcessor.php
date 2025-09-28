<?php

namespace App\Client\Processor;

use App\Dto\GetVideo;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GetVideoProcessor implements GetVideoProcessorInterface
{
    public const GET_VIDEO = '/api/download-video';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(GetVideo $dto): void
    {
        try {
            $response = $this->processorClient->request('POST', self::GET_VIDEO, [
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
