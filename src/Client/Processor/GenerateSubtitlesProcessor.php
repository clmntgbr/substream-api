<?php

namespace App\Client\Processor;

use App\Dto\Processor\GenerateSubtitles;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class GenerateSubtitlesProcessor implements GenerateSubtitlesProcessorInterface
{
    public const GENERATE_SUBTITLES_URL = '/api/generate-subtitles';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(GenerateSubtitles $dto): void
    {
        try {
            $response = $this->processorClient->request('POST', self::GENERATE_SUBTITLES_URL, [
                'json' => $dto->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to generate subtitles');
        }
    }
}
