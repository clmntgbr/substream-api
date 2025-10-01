<?php

namespace App\Dto;

use App\Entity\Stream;
use Symfony\Component\Uid\Uuid;

final class ExtractSound implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'file_name' => $this->stream->getFileName(),
            'stream_id' => (string) $this->stream->getId(),
        ];
    }
}
