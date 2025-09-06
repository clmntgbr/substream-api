<?php

namespace App\Service;

use App\Application\Command\CommandInterface;

interface MessageBusInterface
{
    public function dispatch(CommandInterface $command): void;

    /**
     * @param CommandInterface[] $commands
     */
    public function dispatchs(array $commands): void;
}
