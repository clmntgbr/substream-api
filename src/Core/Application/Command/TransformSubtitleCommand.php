<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class TransformSubtitleCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleSrtFile,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleSrtFile(): string
    {
        return $this->subtitleSrtFile;
    }
}
