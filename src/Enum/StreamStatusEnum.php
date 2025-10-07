<?php

namespace App\Enum;

enum StreamStatusEnum: string
{
    case CREATED = 'created';
    case FAILED = 'failed';
    case COMPLETED = 'completed';
    case DELETED = 'deleted';

    case UPLOADED = 'uploaded';
    case UPLOADING = 'uploading';
    case UPLOAD_FAILED = 'upload_failed';

    case EXTRACTING_SOUND = 'extracting_sound';
    case EXTRACTING_SOUND_FAILED = 'extracting_sound_failed';
    case EXTRACTING_SOUND_COMPLETED = 'extracting_sound_completed';

    case GENERATING_SUBTITLE = 'generating_subtitle';
    case GENERATING_SUBTITLE_FAILED = 'generating_subtitle_failed';
    case GENERATING_SUBTITLE_COMPLETED = 'generating_subtitle_completed';

    case TRANSFORMING_SUBTITLE = 'transforming_subtitle';
    case TRANSFORMING_SUBTITLE_FAILED = 'transforming_subtitle_failed';
    case TRANSFORMING_SUBTITLE_COMPLETED = 'transforming_subtitle_completed';

    case RESIZING_VIDEO = 'resizing_video';
    case RESIZING_VIDEO_FAILED = 'resizing_video_failed';
    case RESIZING_VIDEO_COMPLETED = 'resizing_video_completed';

    case EMBEDDING_VIDEO = 'embedding_video';
    case EMBEDDING_VIDEO_FAILED = 'embedding_video_failed';
    case EMBEDDING_VIDEO_COMPLETED = 'embedding_video_completed';

    case CHUNKING_VIDEO = 'chunking_video';
    case CHUNKING_VIDEO_FAILED = 'chunking_video_failed';
    case CHUNKING_VIDEO_COMPLETED = 'chunking_video_completed';
}
