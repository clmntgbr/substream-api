<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Core\Command;

use App\CoreDD\Application\Core\Message\GetVideoMessage;
use App\CoreDD\Application\Trait\WorkflowTrait;
use App\CoreDD\Domain\Stream\Enum\StreamStatusEnum;
use App\CoreDD\Domain\Stream\Repository\StreamRepository;
use App\CoreDD\Domain\Task\Entity\Task;
use App\CoreDD\Domain\Task\Repository\TaskRepository;
use App\CoreDD\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class GetVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private CoreBusInterface $coreBus,
        private LoggerInterface $logger,
        private TaskRepository $taskRepository,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => GetVideoCommand::class,
            ]);

            return;
        }

        try {
            $task = Task::create(GetVideoCommand::class, $stream);
            $this->taskRepository->saveAndFlush($task);

            $taskId = $task->getId();
            if (null === $taskId) {
                throw new \RuntimeException('Task ID is required');
            }

            $this->coreBus->dispatch(new GetVideoMessage(
                streamId: $stream->getId(),
                taskId: $taskId,
                url: $command->getUrl(),
            ));
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error during video download', [
                'stream_id' => (string) $command->getStreamId(),
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            $stream->markAsFailed(StreamStatusEnum::UPLOAD_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->mercurePublisher->refreshStream($stream, GetVideoCommand::class);
            $this->mercurePublisher->refreshSearchStreams($stream, GetVideoCommand::class);
        }
    }
}
