<?php

declare(strict_types=1);

namespace App\Application\Stream\Event;

use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateStreamEvent extends Event
{
    public function __construct(
        private Uuid $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }
}
