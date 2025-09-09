<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformSubtitles;
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
    public function __invoke(TransformSubtitles $payload): void
    {
        try {
            $response = $this->processorClient->request('POST', self::TRANSFORM_SUBTITLES_URL, [
                'json' => $payload->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to transform subtitles');
        }
    }
}
