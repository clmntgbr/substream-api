<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Message\GetVideoMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Task;
use App\Enum\StreamStatusEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
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
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private CoreBusInterface $coreBus,
        private LoggerInterface $logger,
        private TaskRepository $taskRepository,
        private PublishServiceInterface $publishService,
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

            $this->coreBus->dispatch(new GetVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
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
            $this->publishService->refreshStream($stream, GetVideoCommand::class);
            $this->publishService->refreshSearchStreams($stream, GetVideoCommand::class);
        }
    }
}
