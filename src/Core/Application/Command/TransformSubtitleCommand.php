<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class TransformSubtitleCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleSrtFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleSrtFileName(): string
    {
        return $this->subtitleSrtFileName;
    }
}
