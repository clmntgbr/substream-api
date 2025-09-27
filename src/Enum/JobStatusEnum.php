<?php

declare(strict_types=1);

namespace App\Enum;

enum JobStatusEnum: string
{
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILURE = 'failure';
}
