<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ResumeVideoCommand;
use App\Core\Application\Message\ResumeVideoMessage;
use App\Entity\Stream;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
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
            fn (Stream $stream, Task $task) => new ResumeVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                subtitleSrtFileName: $command->getSubtitleSrtFileName(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::RESUMING;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new ResumeVideoMessage(
            streamId: $stream->getId(),
            taskId: $task->getId(),
            subtitleSrtFileName: $this->currentCommand->getSubtitleSrtFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsResumingFailed();
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
