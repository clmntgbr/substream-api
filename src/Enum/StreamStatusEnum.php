<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case UPLOADED = 'uploaded';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
