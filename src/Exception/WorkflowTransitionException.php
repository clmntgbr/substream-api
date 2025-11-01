<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class WorkflowTransitionException extends BusinessException
{
    public function __construct(string $transition, string $currentState, ?\Throwable $previous = null)
    {
        parent::__construct(
            englishMessage: "Cannot transition to {$transition} from {$currentState}",
            translationKey: 'error.workflow.invalid_transition',
            translationParams: ['transition' => $transition, 'state' => $currentState],
            httpStatusCode: Response::HTTP_CONFLICT,
            previous: $previous
        );
    }
}
