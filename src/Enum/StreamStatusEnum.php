<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case UPLOADING = 'uploading';
    case UPLOADED = 'uploaded';
    case PROCESSED = 'processed';
    case FAILED = 'failed';
}
