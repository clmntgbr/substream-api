<?php

declare(strict_types=1);

namespace App\Core\Application\Core\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class TransformSubtitleCommand implements AsynchronousInterface
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

    public function getStamps(): array
    {
        return [];
    }
}
