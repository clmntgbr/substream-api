<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ChunkVideoCommand;
use App\Core\Application\Message\ChunkVideoMessage;
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
class ChunkVideoCommandHandler
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

    public function __invoke(ChunkVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::CHUNKING_VIDEO);
            $this->streamRepository->save($stream);

            $task = Task::create(ChunkVideoCommand::class, $stream);
            $this->taskRepository->save($task, true);

            $this->coreBus->dispatch(new ChunkVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                chunkNumber: $stream->getOption()->getChunkNumber(),
                embedFileName: $command->getEmbedFileName(),
            ));
        } catch (\Exception) {
            $stream->markAsChunkingVideoFailed();
            $this->streamRepository->save($stream);
        } finally {
            $this->publishService->refreshSearchStreams($stream->getUser());
        }
    }
}
