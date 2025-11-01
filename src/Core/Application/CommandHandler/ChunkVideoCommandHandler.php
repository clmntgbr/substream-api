<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ChunkVideoCommand;
use App\Core\Application\Message\ChunkVideoMessage;
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
class ChunkVideoCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private ChunkVideoCommand $currentCommand;

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

    public function __invoke(ChunkVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            fn (Stream $stream, Task $task) => new ChunkVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                chunkNumber: $stream->getOption()->getChunkNumber(),
                embedFileName: $command->getEmbedFileName(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::CHUNKING_VIDEO;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new ChunkVideoMessage(
            streamId: $stream->getId(),
            taskId: $task->getId(),
            chunkNumber: $stream->getOption()->getChunkNumber(),
            embedFileName: $this->currentCommand->getEmbedFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsChunkingVideoFailed();
    }

    protected function getCommandClass(): string
    {
        return ChunkVideoCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'video chunking';
    }
}
