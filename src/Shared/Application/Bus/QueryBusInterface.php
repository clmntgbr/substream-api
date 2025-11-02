<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface QueryBusInterface
{
    /**
     * @param array<int, \Symfony\Component\Messenger\Stamp\StampInterface> $stamps
     */
    public function dispatch(object $message, array $stamps = []): mixed;
}
