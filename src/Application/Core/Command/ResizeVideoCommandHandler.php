<?php

declare(strict_types=1);

namespace App\Application\Core\Command;

use App\Application\Core\Message\ResizeVideoMessage;
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
class ResizeVideoCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private ResizeVideoCommand $currentCommand;

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

    public function __invoke(ResizeVideoCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::RESIZING_VIDEO;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        $taskId = $task->getId();
        if (null === $taskId) {
            throw new \RuntimeException('Task ID is required');
        }

        return new ResizeVideoMessage(
            streamId: $stream->getId(),
            taskId: $taskId,
            fileName: $this->currentCommand->getFileName(),
            format: $stream->getOption()->getFormat(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsFailed(StreamStatusEnum::RESIZING_VIDEO_FAILED);
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
