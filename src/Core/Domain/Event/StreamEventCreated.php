<?php

declare(strict_types=1);

namespace App\Core\Domain\Event;

use App\Shared\Domain\Event\DomainEventInterface;

/** Event triggered when Stream is modified */
class StreamEventCreated implements DomainEventInterface
{
    public function __construct(
        public readonly \App\Core\Domain\ValueObject\StreamId $id,
        public readonly \DateTimeImmutable $occurredOn
    ) {
    }

    public function occurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
