<?php

namespace App\Enum;

enum WorkflowTransitionEnum: string
{
    case UPLOADING = 'uploading';
    case UPLOADED_SIMPLE = 'uploaded_simple';
    case UPLOADED = 'uploaded';
    case UPLOAD_FAILED = 'upload_failed';

    case EXTRACTING_SOUND = 'extracting_sound';
    case EXTRACTING_SOUND_FAILED = 'extracting_sound_failed';
    case EXTRACTING_SOUND_COMPLETED = 'extracting_sound_completed';
}
