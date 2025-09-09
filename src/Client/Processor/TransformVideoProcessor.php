<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformVideo;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TransformVideoProcessor implements TransformVideoProcessorInterface
{
    public const TRANSFORM_VIDEO_URL = '/api/transform-video';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(TransformVideo $payload): void
    {
        try {
            $response = $this->processorClient->request('POST', self::TRANSFORM_VIDEO_URL, [
                'json' => $payload->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to transform video');
        }
    }
}
