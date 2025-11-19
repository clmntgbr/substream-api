<?php

declare(strict_types=1);

namespace App\Domain\Task\Enum;

enum TaskStatusEnum: string
{
    case RUNNING = 'running';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
