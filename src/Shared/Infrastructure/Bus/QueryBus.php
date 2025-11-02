<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\QueryBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class QueryBus implements QueryBusInterface
{
    public function __construct(private MessageBusInterface $queryBus)
    {
    }

    /**
     * @param array<int, \Symfony\Component\Messenger\Stamp\StampInterface> $stamps
     */
    public function dispatch(object $query, array $stamps = []): mixed
    {
        $envelope = $this->queryBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);

        if (!$handledStamp) {
            throw new \RuntimeException(sprintf('No handler found for query of type "%s".', $query::class));
        }

        return $handledStamp->getResult();
    }
}
