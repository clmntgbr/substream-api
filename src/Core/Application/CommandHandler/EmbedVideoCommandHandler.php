<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\EmbedVideoCommand;
use App\Core\Application\Message\EmbedVideoMessage;
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
class EmbedVideoCommandHandler
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

    public function __invoke(EmbedVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }
        try {
            $this->apply($stream, WorkflowTransitionEnum::EMBEDDING_VIDEO);
            $this->streamRepository->save($stream);

            $task = Task::create(EmbedVideoCommand::class, $stream);
            $this->taskRepository->save($task, true);

            $this->coreBus->dispatch(new EmbedVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                subtitleAssFileName: $command->getSubtitleAssFileName(),
                resizeFileName: $command->getResizeFileName(),
            ));
        } catch (\Exception) {
            $stream->markAsEmbeddingVideoFailed();
            $this->streamRepository->save($stream);
        } finally {
            $this->publishService->dispatchSearchStreams($stream->getUser(), EmbedVideoCommand::class);
        }
    }
}
