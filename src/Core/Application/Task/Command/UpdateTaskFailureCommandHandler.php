<?php

declare(strict_types=1);

namespace App\Core\Application\Task\Command;

use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Task\Repository\TaskRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class UpdateTaskFailureCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private TaskRepository $taskRepository,
    ) {
    }

    public function __invoke(UpdateTaskFailureCommand $command): void
    {
        $task = $this->taskRepository->findByUuid($command->getTaskId());

        if (null === $task) {
            $this->logger->error('Task not found', [
                'task_id' => $command->getTaskId(),
            ]);

            return;
        }

        $task->markAsFailed();
        $this->taskRepository->saveAndFlush($task);
    }
}
