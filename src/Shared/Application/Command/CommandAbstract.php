<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

abstract class CommandAbstract
{
    /**
     * @return array<int, \Symfony\Component\Messenger\Stamp\StampInterface>
     */
    public function getStamps(): array
    {
        return [];
    }
}
