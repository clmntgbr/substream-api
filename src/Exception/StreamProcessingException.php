<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class StreamProcessingException extends BusinessException
{
    public function __construct(string $streamId, string $reason, ?\Throwable $previous = null)
    {
        parent::__construct(
            englishMessage: "Stream processing failed: {$reason}",
            translationKey: ErrorKeyEnum::STREAM_PROCESSING_FAILED->value,
            translationParams: ['stream_id' => $streamId, 'reason' => $reason],
            httpStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            previous: $previous
        );
    }
}
