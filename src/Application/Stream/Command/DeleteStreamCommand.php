<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Symfony\Component\Uid\Uuid;

final class DeleteStreamCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    /**
     * @return array<DelayStamp>
     */
    public function getStamps(): array
    {
        return [];
    }
}
