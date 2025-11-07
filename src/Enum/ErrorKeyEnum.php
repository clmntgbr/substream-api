<?php

declare(strict_types=1);

namespace App\Enum;

enum ErrorKeyEnum: string
{
    case AUTH_INVALID_CREDENTIALS = 'error.auth.invalid_credentials';
    case AUTH_ACCOUNT_LOCKED = 'error.auth.account_locked';
    case AUTH_ACCOUNT_DISABLED = 'error.auth.account_disabled';
    case AUTH_TOKEN_INVALID = 'error.auth.token_invalid';
    case AUTH_TOKEN_MISSING = 'error.auth.token_missing';
    case AUTH_TOKEN_EXPIRED = 'error.auth.token_expired';

    case OPTION_NOT_FOUND = 'error.option.not_found';

    case THUMBNAIL_INVALID_FORMAT = 'error.thumbnail.invalid_format';
    case THUMBNAIL_FILE_CREATION_FAILED = 'error.thumbnail.file_creation_failed';

    case VALIDATION_FAILED = 'error.validation.failed';
    case VALIDATION_OPTION_ID_REQUIRED = 'error.validation.option_id.required';
    case VALIDATION_OPTION_ID_INVALID = 'error.validation.option_id.invalid';
    case VALIDATION_DURATION_REQUIRED = 'error.validation.duration.required';
    case VALIDATION_DURATION_INVALID = 'error.validation.duration.invalid';
    case VALIDATION_DURATION_MUST_BE_POSITIVE = 'error.validation.duration.must_be_positive';

    case FILE_INVALID = 'error.file.invalid';
    case FILE_INVALID_MIME_TYPE = 'error.file.invalid_mime_type';
    case FILE_TOO_LARGE = 'error.file.too_large';
    case FILE_UPLOAD_FAILED = 'error.file.upload_failed';

    case PROCESSOR_FAILED = 'error.processor.failed';
    case PROCESSOR_STEP_FAILED = 'error.processor.step_failed';

    case OAUTH_FAILED = 'error.oauth.failed';
    case OAUTH_PROVIDER_FAILED = 'error.oauth.provider_failed';
    case OAUTH_INVALID_STATE = 'error.oauth.invalid_state';
    case OAUTH_TOKEN_FAILED = 'error.oauth.token_failed';

    case STREAM_PROCESSING_FAILED = 'error.stream.processing_failed';
    case STREAM_NOT_DOWNLOADABLE = 'error.stream.not_downloadable';
    case STREAM_NOT_FOUND = 'error.stream.not_found';

    case TASK_CREATION_FAILED = 'error.task.creation_failed';

    case WORKFLOW_INVALID_TRANSITION = 'error.workflow.invalid_transition';

    case SERVER_INTERNAL = 'error.server.internal';
    case SERVER_INTERNAL_DEBUG = 'error.server.internal_debug';

    public static function httpStatus(int $status): string
    {
        return sprintf('error.http.%d', $status);
    }

    public static function validationInvalidFormat(string $fileType): string
    {
        return sprintf('error.validation.%s.invalid_format', $fileType);
    }

    public static function validationTooLarge(string $fileType): string
    {
        return sprintf('error.validation.%s.too_large', $fileType);
    }
}
