<?php

namespace App\Client\Processor;

use App\Dto\ExtractSound;
use App\Exception\ProcessorException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ExtractSoundProcessor implements ExtractSoundProcessorInterface
{
    public const EXTRACT_SOUND = '/api/extract-sound';

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
        try {
            $response = $this->processorClient->request('POST', self::EXTRACT_SOUND, [
                'json' => $dto->jsonSerialize(),
                'headers' => [
                    'Authorization' => $this->processorToken,
                ],
            ]);

            if (200 !== $response->getStatusCode()) {
                throw new \Exception();
            }
        } catch (\Exception $_) {
            throw new ProcessorException('Failed to extract sound');
        }
    }
}
