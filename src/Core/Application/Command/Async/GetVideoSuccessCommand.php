<?php

namespace App\Core\Application\Command\Async;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class GetVideoSuccessCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
        private string $originalFileName,
        private string $mimeType,
        private int $size,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
