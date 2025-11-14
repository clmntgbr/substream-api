<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Application\Core\Message\TransformSubtitleMessage;
use App\Application\StreamWorkflow\Command\AbstractStreamWorkflowCommandHandler;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class TransformSubtitleCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private TransformSubtitleCommand $currentCommand;

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

    public function __invoke(TransformSubtitleCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            function (Stream $stream, Task $task) use ($command) {
                $taskId = $task->getId();
                if (null === $taskId) {
                    throw new \RuntimeException('Task ID is required');
                }

                return new TransformSubtitleMessage(
                    taskId: $taskId,
                    streamId: $stream->getId(),
                    option: $stream->getOption(),
                    subtitleSrtFileName: $command->getSubtitleSrtFileName(),
                );
            }
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::TRANSFORMING_SUBTITLE;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        $taskId = $task->getId();
        if (null === $taskId) {
            throw new \RuntimeException('Task ID is required');
        }

        return new TransformSubtitleMessage(
            taskId: $taskId,
            streamId: $stream->getId(),
            option: $stream->getOption(),
            subtitleSrtFileName: $this->currentCommand->getSubtitleSrtFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsFailed(StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED);
    }

    protected function getCommandClass(): string
    {
        return TransformSubtitleCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'subtitle transformation';
    }
}
