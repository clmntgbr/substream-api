<?php

namespace App\Enum;

enum WorkflowTransitionEnum: string
{
    case UPLOADING = 'uploading';
    case UPLOADED_SIMPLE = 'uploaded_simple';
    case UPLOADED = 'uploaded';
    case UPLOAD_FAILED = 'upload_failed';
}
