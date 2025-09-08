<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case UPLOADING = 'uploading';
    case UPLOADED = 'uploaded';
    case UPLOAD_FAILED = 'upload_failed';
    
    case EXTRACTING_SOUND_PROCESSING = 'extracting_sound_processing';
    case EXTRACTED_SOUND = 'extracted_sound';
    case EXTRACTED_SOUND_FAILED = 'extracted_sound_failed';

    case GENERATING_SUBTITLES_PROCESSING = 'generating_subtitles_processing';
    case GENERATED_SUBTITLES = 'generated_subtitles';
    case GENERATED_SUBTITLES_FAILED = 'generated_subtitles_failed';

    case TRANSFORMING_SUBTITLES_PROCESSING = 'transforming_subtitles_processing';
    case TRANSFORMED_SUBTITLES = 'transformed_subtitles';
    case TRANSFORMED_SUBTITLES_FAILED = 'transformed_subtitles_failed';
}
