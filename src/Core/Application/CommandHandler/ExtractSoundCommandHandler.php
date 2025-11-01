<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Message\ExtractSoundMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use App\Service\PublishServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class ExtractSoundCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
        private TaskRepository $taskRepository,
        private PublishServiceInterface $publishService,
    ) {
    }

    public function __invoke(ExtractSoundCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => ExtractSoundCommand::class,
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND);
            $this->streamRepository->save($stream);

            $task = Task::create(ExtractSoundCommand::class, $stream);
            $this->taskRepository->save($task, true);

            $this->coreBus->dispatch(new ExtractSoundMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                fileName: $command->getFileName(),
            ));
        } catch (\Exception) {
            $stream->markAsExtractingSoundFailed();
            $this->streamRepository->save($stream);
        } finally {
            $this->publishService->refreshStream($stream, ExtractSoundCommand::class);
            $this->publishService->refreshSearchStreams($stream, ExtractSoundCommand::class);
        }
    }
}
