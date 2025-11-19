<?php

declare(strict_types=1);

namespace App\Shared\Application\Command;

use Symfony\Component\Messenger\Stamp\StampInterface;

interface AsynchronousPriorityInterface
{
    /**
     * @return StampInterface[]
     */
    public function getStamps(): array;
}
