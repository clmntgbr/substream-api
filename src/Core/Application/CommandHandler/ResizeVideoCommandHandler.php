<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ResizeVideoCommand;
use App\Core\Application\Message\ResizeVideoMessage;
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
class ResizeVideoCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private ResizeVideoCommand $currentCommand;

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

    public function __invoke(ResizeVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            fn (Stream $stream, Task $task) => new ResizeVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                fileName: $command->getFileName(),
                format: $stream->getOption()->getFormat(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::RESIZING_VIDEO;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new ResizeVideoMessage(
            streamId: $stream->getId(),
            taskId: $task->getId(),
            fileName: $this->currentCommand->getFileName(),
            format: $stream->getOption()->getFormat(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsResizingVideoFailed();
    }

    protected function getCommandClass(): string
    {
        return ResizeVideoCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'video resizing';
    }
}
