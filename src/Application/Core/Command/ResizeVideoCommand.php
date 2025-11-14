<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class ResizeVideoCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $fileName,
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

    public function getStamps(): array
    {
        return [];
    }
}
