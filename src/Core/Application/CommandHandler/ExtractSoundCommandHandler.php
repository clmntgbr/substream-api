<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Message\ExtractSoundMessage;
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
class ExtractSoundCommandHandler extends AbstractStreamWorkflowCommandHandler
{
    private ExtractSoundCommand $currentCommand;

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

    public function __invoke(ExtractSoundCommand $command): void
    {
        $this->currentCommand = $command;

        $this->executeWorkflow(
            $command->getStreamId(),
            fn (Stream $stream, Task $task) => new ExtractSoundMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                fileName: $command->getFileName(),
            )
        );
    }

    protected function getTransition(): WorkflowTransitionEnum
    {
        return WorkflowTransitionEnum::EXTRACTING_SOUND;
    }

    protected function createMessage(Stream $stream, Task $task): object
    {
        return new ExtractSoundMessage(
            streamId: $stream->getId(),
            taskId: $task->getId(),
            fileName: $this->currentCommand->getFileName(),
        );
    }

    protected function markStreamAsFailed(Stream $stream): void
    {
        $stream->markAsExtractingSoundFailed();
    }

    protected function getCommandClass(): string
    {
        return ExtractSoundCommand::class;
    }

    protected function getWorkflowActionName(): string
    {
        return 'sound extraction';
    }
}
