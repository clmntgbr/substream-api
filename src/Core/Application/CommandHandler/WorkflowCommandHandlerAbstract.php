<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Entity\Job;
use App\Entity\Stream;
use App\Enum\JobStatusEnum;
use App\Enum\StreamStatusEnum;
use App\Repository\JobRepository;
use App\Service\JobContextService;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class WorkflowCommandHandlerAbstract
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
