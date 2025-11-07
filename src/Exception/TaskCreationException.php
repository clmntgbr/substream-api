<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class TaskCreationException extends BusinessException
{
    public function __construct(string $taskType, string $streamId, ?\Throwable $previous = null)
    {
        parent::__construct(
            englishMessage: "Failed to create task {$taskType} for stream {$streamId}",
            translationKey: ErrorKeyEnum::TASK_CREATION_FAILED->value,
            translationParams: ['task_type' => $taskType, 'stream_id' => $streamId],
            httpStatusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
            previous: $previous
        );
    }
}
