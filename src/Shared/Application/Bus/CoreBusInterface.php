<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus;

interface CoreBusInterface
{
    public function dispatch(object $message): mixed;
}
