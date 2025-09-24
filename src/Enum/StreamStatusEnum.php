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

    case TRANSFORMING_SUBTITLE_PROCESSING = 'transforming_subtitle_processing';
    case TRANSFORMED_SUBTITLE = 'transformed_subtitle';
    case TRANSFORMED_SUBTITLE_FAILED = 'transformed_subtitle_failed';

    case TRANSFORMING_VIDEO_PROCESSING = 'transforming_video_processing';
    case TRANSFORMED_VIDEO = 'transformed_video';
    case TRANSFORMED_VIDEO_FAILED = 'transformed_video_failed';

    case GENERATING_VIDEO_PROCESSING = 'generating_video_processing';
    case GENERATED_VIDEO = 'generated_video';
    case GENERATED_VIDEO_FAILED = 'generated_video_failed';

    case FAILED = 'failed';
    case COMPLETED = 'completed';
}
