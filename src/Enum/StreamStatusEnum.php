<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case CREATED = 'created';
    case FAILED = 'failed';
    case COMPLETED = 'completed';

    case UPLOADED = 'uploaded';
    case UPLOAD_FAILED = 'upload_failed';

    case EXTRACTING_SOUND = 'extracting_sound';
    case EXTRACTING_SOUND_FAILED = 'extracting_sound_failed';
    case EXTRACTING_SOUND_COMPLETED = 'extracting_sound_completed';
}
