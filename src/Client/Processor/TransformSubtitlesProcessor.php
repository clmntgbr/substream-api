<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformSubtitles;
use App\Dto\Processor\GenerateSubtitles;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TransformSubtitlesProcessor implements TransformSubtitlesProcessorInterface
{
    public const TRANSFORM_SUBTITLES_URL = '/api/transform-subtitles';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(TransformSubtitles $dto): void
    {
        $response = $this->processorClient->request('POST', self::TRANSFORM_SUBTITLES_URL, [
            'json' => $dto->jsonSerialize(),
            'headers' => [
                'Authorization' => $this->processorToken,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new ProcessorException('Failed to transform subtitles');
        }
    }
}
