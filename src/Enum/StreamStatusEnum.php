<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case PENDING = 'pending';
    case CREATED = 'created';
    case CREATED_FAILED = 'created_failed';
}
