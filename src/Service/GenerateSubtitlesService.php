<?php

namespace App\Service;

use App\Client\Processor\GenerateSubtitlesProcessorInterface;
use App\Dto\Processor\GenerateSubtitles;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;

class GenerateSubtitlesService implements GenerateSubtitlesServiceInterface
{
    public function __construct(
        private GenerateSubtitlesProcessorInterface $generateSubtitlesProcessor,
        private StreamRepository $streamRepository,
    ) {
    }

    public function generateSubtitles(Stream $stream): void
    {
        try {
            ($this->generateSubtitlesProcessor)(new GenerateSubtitles(
                stream: $stream,
            ));
        } catch (ProcessorException $_) {
            $stream->markAsFailed(StreamStatusEnum::GENERATED_SUBTITLES_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
