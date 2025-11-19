<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class EmbedVideoCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $subtitleAssFileName,
        private string $resizeFileName,
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

    public function getResizeFileName(): string
    {
        return $this->resizeFileName;
    }

    public function getStamps(): array
    {
        return [];
    }
}
