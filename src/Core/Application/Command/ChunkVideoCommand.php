<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ChunkVideoCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private int $chunkNumber,
        private string $embedFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getChunkNumber(): int
    {
        return $this->chunkNumber;
    }

    public function getEmbedFileName(): string
    {
        return $this->embedFileName;
    }
}
