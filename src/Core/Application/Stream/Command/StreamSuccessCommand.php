<?php

declare(strict_types=1);

namespace App\Core\Application\Stream\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class StreamSuccessCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getStamps(): array
    {
        return [];
    }
}
