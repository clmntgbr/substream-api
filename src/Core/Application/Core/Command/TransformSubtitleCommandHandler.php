<?php

declare(strict_types=1);

namespace App\Core\Application\Core\Command;

use App\Core\Application\Core\Message\TransformSubtitleMessage;
use App\Core\Application\StreamWorkflow\Command\AbstractStreamWorkflowCommandHandler;
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
class TransformSubtitleCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private TransformSubtitleCommand $currentCommand;

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
