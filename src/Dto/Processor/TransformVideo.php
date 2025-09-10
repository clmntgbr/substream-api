<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class TransformVideo implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->stream->getId(),
            'video_file' => $this->stream->getFileName(),
            'options' => [
                'video_format' => $this->stream->getOptions()->getVideoFormat(),
                'video_parts' => $this->stream->getOptions()->getVideoParts(),
            ],
        ];
    }
}
