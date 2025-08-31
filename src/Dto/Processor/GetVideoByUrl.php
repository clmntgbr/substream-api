<?php

namespace App\Dto\Processor;

use App\Entity\Stream;

final class GetVideoByUrl implements \JsonSerializable
{ 
    public function __construct(
        public readonly Stream $stream,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->stream->getUrl(),
            'streamId' => (string) $this->stream->getId(),
        ];
    }
}