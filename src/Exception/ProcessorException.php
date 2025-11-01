<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class ProcessorException extends BusinessException
{
    public function __construct(
        string $englishMessage = 'Processing failed',
        string $translationKey = 'error.processor.failed',
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public static function processingFailed(string $step, string $reason = ''): self
    {
        return new self(
            "Processing step \"{$step}\" failed".($reason ? ": {$reason}" : ''),
            'error.processor.step_failed',
            [
                'step' => $step,
                'reason' => $reason,
            ]
        );
    }
}
