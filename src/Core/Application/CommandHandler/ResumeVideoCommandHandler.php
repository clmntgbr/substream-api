<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ResumeVideoCommand;
use App\Core\Application\Message\ResumeVideoMessage;
use App\Core\Domain\Stream\Entity\Stream;
use App\Core\Domain\Stream\Enum\StreamStatusEnum;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Core\Domain\Task\Entity\Task;
use App\Core\Domain\Task\Repository\TaskRepository;
use App\Enum\WorkflowTransitionEnum;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class ResumeVideoCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private ResumeVideoCommand $currentCommand;

    public function __construct(
        StreamRepository $streamRepository,
        WorkflowInterface $streamsStateMachine,
        LoggerInterface $logger,
        CoreBusInterface $coreBus,
        TaskRepository $taskRepository,
        PublishServiceInterface $publishService,
    ) {
        parent::__construct(
            $streamRepository,
            $streamsStateMachine,
            $logger,
            $coreBus,
            $taskRepository,
            $publishService
        );
    }

    public function __invoke(ResumeVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            function (Stream $stream, Task $task) use ($command) {
                $taskId = $task->getId();
                if (null === $taskId) {
                    throw new \RuntimeException('Task ID is required');
                }

                return new ResumeVideoMessage(
                    streamId: $stream->getId(),
                    taskId: $taskId,
                    subtitleSrtFileName: $command->getSubtitleSrtFileName(),
                );
            }
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::RESUMING;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        $taskId = $task->getId();
        if (null === $taskId) {
            throw new \RuntimeException('Task ID is required');
        }

        return new ResumeVideoMessage(
            streamId: $stream->getId(),
            taskId: $taskId,
            subtitleSrtFileName: $this->currentCommand->getSubtitleSrtFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsFailed(StreamStatusEnum::RESUMING_FAILED);
    }

    protected function getCommandClass(): string
    {
        return ResumeVideoCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'video resuming';
    }
}
