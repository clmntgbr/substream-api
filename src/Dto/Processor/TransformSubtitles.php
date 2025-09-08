<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class TransformSubtitles implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_id' => (string) $this->stream->getId(),
            'subtitle_srt_file' => $this->stream->getSubtitleSrtFile(),
        ];
    }
}
