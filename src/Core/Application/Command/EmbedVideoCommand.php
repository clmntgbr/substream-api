<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class EmbedVideoCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleAssFileName,
        private string $resizedFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getSubtitleAssFileName(): string
    {
        return $this->subtitleAssFileName;
    }

    public function getResizedFileName(): string
    {
        return $this->resizedFileName;
    }
}
