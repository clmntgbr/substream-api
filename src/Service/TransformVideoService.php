<?php

namespace App\Service;

use App\Client\Processor\TransformVideoProcessorInterface;
use App\Dto\Processor\TransformVideo;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;

class TransformVideoService implements TransformVideoServiceInterface
{
    public function __construct(
        private TransformVideoProcessorInterface $transformVideoProcessor,
        private StreamRepository $streamRepository,
    ) {
    }

    public function transformVideo(Stream $stream): void
    {
        try {
            ($this->transformVideoProcessor)(new TransformVideo(
                stream: $stream,
            ));
        } catch (ProcessorException $_) {
            $stream->markAsFailed(StreamStatusEnum::TRANSFORMED_VIDEO_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
