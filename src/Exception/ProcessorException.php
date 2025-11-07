<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class ProcessorException extends BusinessException
{
    /**
     * @param array<string, mixed> $translationParams
     */
    public function __construct(
        string $englishMessage = 'Processing failed',
        string $translationKey = ErrorKeyEnum::PROCESSOR_FAILED->value,
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function processingFailed(string $step, string $reason = ''): self
    {
        return new self(
            "Processing step \"{$step}\" failed".($reason ? ": {$reason}" : ''),
            ErrorKeyEnum::PROCESSOR_STEP_FAILED->value,
            [
                'step' => $step,
                'reason' => $reason,
            ]
        );
    }
}
