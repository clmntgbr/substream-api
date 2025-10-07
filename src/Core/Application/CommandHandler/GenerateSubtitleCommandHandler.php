<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GenerateSubtitleCommand;
use App\Core\Application\Message\GenerateSubtitleMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class GenerateSubtitleCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
        private TaskRepository $taskRepository,
    ) {
    }

    public function __invoke(GenerateSubtitleCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE);
        $this->streamRepository->save($stream);

        $task = Task::create(GenerateSubtitleCommand::class, $stream);
        $this->taskRepository->save($task, true);

        $this->coreBus->dispatch(new GenerateSubtitleMessage(
            taskId: $task->getId(),
            streamId: $stream->getId(),
            audioFiles: $command->getAudioFiles(),
        ));
    }
}
