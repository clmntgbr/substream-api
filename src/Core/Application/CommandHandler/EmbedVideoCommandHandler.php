<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\EmbedVideoCommand;
use App\Core\Application\Message\EmbedVideoMessage;
use App\Entity\Stream;
use App\Entity\Task;
use App\Enum\StreamStatusEnum;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class EmbedVideoCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private EmbedVideoCommand $currentCommand;

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

    public function __invoke(EmbedVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            fn (Stream $stream, Task $task) => new EmbedVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                subtitleAssFileName: $command->getSubtitleAssFileName(),
                resizeFileName: $command->getResizeFileName(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::EMBEDDING_VIDEO;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new EmbedVideoMessage(
            streamId: $stream->getId(),
            taskId: $task->getId(),
            subtitleAssFileName: $this->currentCommand->getSubtitleAssFileName(),
            resizeFileName: $this->currentCommand->getResizeFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsFailed(StreamStatusEnum::EMBEDDING_VIDEO_FAILED);
    }

    protected function getCommandClass(): string
    {
        return EmbedVideoCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'video embedding';
    }
}
