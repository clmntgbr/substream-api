<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class ExtractSound implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_video_file_name_name' => $this->stream->getVideoFileName(),
            'stream_id' => (string) $this->stream->getId(),
        ];
    }
}
