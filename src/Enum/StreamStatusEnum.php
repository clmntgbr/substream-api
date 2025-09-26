<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case CREATED = 'created';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
