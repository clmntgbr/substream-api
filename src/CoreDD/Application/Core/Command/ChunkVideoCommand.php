<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class ChunkVideoCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $embedFileName,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getEmbedFileName(): string
    {
        return $this->embedFileName;
    }

    public function getStamps(): array
    {
        return [];
    }
}
