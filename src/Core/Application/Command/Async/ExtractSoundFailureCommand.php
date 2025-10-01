<?php

namespace App\Core\Application\Command\Async;

use App\Shared\Application\Command\AsyncCommandInterface;
use Symfony\Component\Uid\Uuid;

readonly class ExtractSoundFailureCommand implements AsyncCommandInterface
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }
}
