<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use Symfony\Component\Workflow\WorkflowInterface;

trait WorkflowTrait
{
    public function __construct(
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function canApply(Stream $stream, StreamStatusEnum $transition): bool
    {
        return $this->streamsStateMachine->can($stream, $transition->value);
    }

    public function apply(Stream $stream, StreamStatusEnum $transition): void
    {
        $this->streamsStateMachine->apply($stream, $transition->value);
    }
}
