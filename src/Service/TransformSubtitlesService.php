<?php

namespace App\Service;

use App\Client\Processor\TransformSubtitlesProcessorInterface;
use App\Dto\Processor\TransformSubtitles;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;

class TransformSubtitlesService implements TransformSubtitlesServiceInterface
{
    public function __construct(
        private TransformSubtitlesProcessorInterface $transformSubtitlesProcessor,
        private StreamRepository $streamRepository,
    ) {
    }

    public function transformSubtitles(Stream $stream): void
    {
        try {
            ($this->transformSubtitlesProcessor)(new TransformSubtitles(
                stream: $stream,
            ));
        } catch (ProcessorException $_) {
            $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_SUBTITLES_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
