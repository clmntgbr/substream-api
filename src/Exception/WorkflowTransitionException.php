<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class WorkflowTransitionException extends BusinessException
{
    public function __construct(string $transition, string $currentState, ?\Throwable $previous = null)
    {
        parent::__construct(
            englishMessage: "Cannot transition to {$transition} from {$currentState}",
            translationKey: ErrorKeyEnum::WORKFLOW_INVALID_TRANSITION->value,
            translationParams: ['transition' => $transition, 'state' => $currentState],
            httpStatusCode: Response::HTTP_CONFLICT,
            previous: $previous
        );
    }
}
