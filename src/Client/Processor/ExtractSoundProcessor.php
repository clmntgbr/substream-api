<?php

namespace App\Client\Processor;

use App\Dto\Processor\ExtractSound;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExtractSoundProcessor implements ExtractSoundProcessorInterface
{
    public const EXTRACT_SOUND_URL = '/api/extract-sound';

    public function __construct(
        private HttpClientInterface $processorClient,
        private string $processorToken,
    ) {
    }

    /**
     * @throws ProcessorException
     */
    public function __invoke(ExtractSound $dto): void
    {
        $response = $this->processorClient->request('POST', self::EXTRACT_SOUND_URL, [
            'json' => $dto->jsonSerialize(),
            'headers' => [
                'Authorization' => $this->processorToken,
            ],
        ]);

        if (200 !== $response->getStatusCode()) {
            throw new ProcessorException('Failed to extract sound');
        }
    }
}
