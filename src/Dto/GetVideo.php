<?php

namespace App\Dto;

use App\Entity\Stream;
use Symfony\Component\Uid\Uuid;

final class GetVideo implements \JsonSerializable
{
    public function __construct(
        public readonly Stream $stream,
        public readonly Uuid $jobId,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'url' => $this->stream->getUrl(),
            'stream_id' => (string) $this->stream->getId(),
            'job_id' => (string) $this->jobId,
        ];
    }
}
