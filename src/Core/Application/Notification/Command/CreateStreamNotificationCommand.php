<?php

declare(strict_types=1);

namespace App\Core\Application\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class CreateStreamNotificationCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
        private string $status,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getStamps(): array
    {
        return [];
    }
}
