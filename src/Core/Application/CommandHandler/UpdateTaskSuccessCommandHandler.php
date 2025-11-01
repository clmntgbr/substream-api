<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class UpdateTaskSuccessCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
        private TaskRepository $taskRepository,
    ) {
    }

    public function __invoke(UpdateTaskSuccessCommand $command): void
    {
        $task = $this->taskRepository->findByUuid($command->getTaskId());

        if (null === $task) {
            $this->logger->error('Task not found', [
                'task_id' => $command->getTaskId(),
            ]);

            return;
        }

        $task->setProcessingTime($command->getProcessingTime());
        $task->markAsCompleted();
        $this->taskRepository->saveAndFlush($task);
    }
}
