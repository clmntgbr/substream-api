<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Core\Application\Message\TransformSubtitleMessage;
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
            fn (Stream $stream, Task $task) => new TransformSubtitleMessage(
                taskId: $task->getId(),
                streamId: $stream->getId(),
                option: $stream->getOption(),
                subtitleSrtFileName: $command->getSubtitleSrtFileName(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::TRANSFORMING_SUBTITLE;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new TransformSubtitleMessage(
            taskId: $task->getId(),
            streamId: $stream->getId(),
            option: $stream->getOption(),
            subtitleSrtFileName: $this->currentCommand->getSubtitleSrtFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsTransformingSubtitleFailed();
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
