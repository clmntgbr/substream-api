<?php

namespace App\Dto;

use App\Entity\Stream;

final class GetVideo implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->stream->getUrl(),
            'stream_id' => (string) $this->stream->getId(),
        ];
    }
}
