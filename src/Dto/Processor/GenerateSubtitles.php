<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class GenerateSubtitles implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'stream_file_name' => $this->stream->getFileName(),
            'stream_id' => (string) $this->stream->getId(),
            'audio_files' => $this->stream->getAudioFiles(),
        ];
    }
}
