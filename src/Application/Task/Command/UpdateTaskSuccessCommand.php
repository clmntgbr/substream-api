<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

use App\Shared\Application\Command\AsynchronousInterface;
use Symfony\Component\Uid\Uuid;

final class UpdateTaskSuccessCommand implements AsynchronousInterface
{
    public function __construct(
        private Uuid $taskId,
        private int $processingTime,
    ) {
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }

    public function getProcessingTime(): int
    {
        return $this->processingTime;
    }

    public function getStamps(): array
    {
        return [];
    }
}
