<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateTaskFailureCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $taskId,
    ) {
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }

    public function getStamps(): array
    {
        return [];
    }
}
