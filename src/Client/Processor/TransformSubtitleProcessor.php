<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformSubtitle;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TransformSubtitleProcessor implements TransformSubtitleProcessorInterface
{
    public const TRANSFORM_SUBTITLE_URL = '/api/transform-subtitle';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(TransformSubtitle $payload): void
    {
        try {
            $response = $this->processorClient->request('POST', self::TRANSFORM_SUBTITLE_URL, [
                'json' => $payload->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to transform subtitle');
        }
    }
}
