<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ResizeVideoCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
        private string $format,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getFormat(): string
    {
        return $this->format;
    }
}
