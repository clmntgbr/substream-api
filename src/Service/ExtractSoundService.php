<?php

namespace App\Service;

use App\Client\Processor\ExtractSoundProcessorInterface;
use App\Dto\Processor\ExtractSound;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;

class ExtractSoundService implements ExtractSoundServiceInterface
{
    public function __construct(
        private ExtractSoundProcessorInterface $extractSoundProcessor,
        private StreamRepository $streamRepository,
    ) {
    }

    public function extractSound(Stream $stream): void
    {
        try {
            ($this->extractSoundProcessor)(new ExtractSound(
                stream: $stream,
            ));
        } catch (ProcessorException $_) {
            $stream->markAsFailed(StreamStatusEnum::EXTRACTED_SOUND_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
