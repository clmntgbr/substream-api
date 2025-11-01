<?php

declare(strict_types=1);

namespace App\Enum;

enum TaskStatusEnum: string
{
    case RUNNING = 'running';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
