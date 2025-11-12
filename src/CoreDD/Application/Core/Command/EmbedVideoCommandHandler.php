<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Core\Command;

use App\CoreDD\Application\Core\Message\EmbedVideoMessage;
use App\CoreDD\Application\StreamWorkflow\Command\AbstractStreamWorkflowCommandHandler;
use App\CoreDD\Domain\Stream\Entity\Stream;
use App\CoreDD\Domain\Stream\Enum\StreamStatusEnum;
use App\CoreDD\Domain\Stream\Repository\StreamRepository;
use App\CoreDD\Domain\Task\Entity\Task;
use App\CoreDD\Domain\Task\Repository\TaskRepository;
use App\CoreDD\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Enum\WorkflowTransitionEnum;
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
        MercurePublisherInterface $mercurePublisher,
    ) {
        parent::__construct(
            $streamRepository,
            $streamsStateMachine,
            $logger,
            $coreBus,
            $taskRepository,
            $mercurePublisher
        );
    }

    public function __invoke(EmbedVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            function (Stream $stream, Task $task) use ($command) {
                $taskId = $task->getId();
                if (null === $taskId) {
                    throw new \RuntimeException('Task ID is required');
                }

                return new EmbedVideoMessage(
                    streamId: $stream->getId(),
                    taskId: $taskId,
                    subtitleAssFileName: $command->getSubtitleAssFileName(),
                    resizeFileName: $command->getResizeFileName(),
                );
            }
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::EMBEDDING_VIDEO;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        $taskId = $task->getId();
        if (null === $taskId) {
            throw new \RuntimeException('Task ID is required');
        }

        return new EmbedVideoMessage(
            streamId: $stream->getId(),
            taskId: $taskId,
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
