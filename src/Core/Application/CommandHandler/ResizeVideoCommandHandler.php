<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\ResizeVideoCommand;
use App\Core\Application\Message\ResizeVideoMessage;
use App\Core\Application\Trait\WorkflowTrait;
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
class ResizeVideoCommandHandler
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

    public function __invoke(ResizeVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => ResizeVideoCommand::class,
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::RESIZING_VIDEO);
            $this->streamRepository->saveAndFlush($stream);

            $task = Task::create(ResizeVideoCommand::class, $stream);
            $this->taskRepository->saveAndFlush($task, true);

            $this->coreBus->dispatch(new ResizeVideoMessage(
                streamId: $stream->getId(),
                taskId: $task->getId(),
                fileName: $command->getFileName(),
                format: $stream->getOption()->getFormat(),
            ));
        } catch (\Exception) {
            $stream->markAsResizingVideoFailed();
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->publishService->refreshStream($stream, ResizeVideoCommand::class);
            $this->publishService->refreshSearchStreams($stream, ResizeVideoCommand::class);
        }
    }
}
