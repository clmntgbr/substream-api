<?php

namespace App\Service;

use App\Client\Processor\TransformSubtitleProcessorInterface;
use App\Dto\Processor\TransformSubtitle;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;

class TransformSubtitleService implements TransformSubtitleServiceInterface
{
    public function __construct(
        private TransformSubtitleProcessorInterface $transformSubtitleProcessor,
        private StreamRepository $streamRepository,
    ) {
    }

    public function transformSubtitle(Stream $stream): void
    {
        try {
            ($this->transformSubtitleProcessor)(new TransformSubtitle(
                stream: $stream,
            ));
        } catch (ProcessorException $_) {
            $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_SUBTITLE_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
