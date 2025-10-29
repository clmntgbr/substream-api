<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use App\Entity\Stream;
use App\Enum\WorkflowTransitionEnum;
use Symfony\Component\Workflow\WorkflowInterface;

trait WorkflowTrait
{
    public function __construct(
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function canApply(Stream $stream, WorkflowTransitionEnum $transition): bool
    {
        return $this->streamsStateMachine->can($stream, $transition->value);
    }

    public function apply(Stream $stream, WorkflowTransitionEnum $transition): void
    {
        if (!$this->canApply($stream, $transition)) {
            throw new \InvalidArgumentException(sprintf('Transition "%s" cannot be applied to stream "%s"', $transition->value, $stream->getId()));
        }

        $this->streamsStateMachine->apply($stream, $transition->value);
    }
}
