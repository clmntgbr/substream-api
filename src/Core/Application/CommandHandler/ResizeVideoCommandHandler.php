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
use Symfony\Component\Workflow\Exception\TransitionException as WorkflowTransitionException;
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
        } catch (WorkflowTransitionException $e) {
            $this->logger->error('Workflow transition failed during video resizing', [
                'stream_id' => (string) $command->getStreamId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $stream->markAsResizingVideoFailed();
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error during video resizing', [
                'stream_id' => (string) $command->getStreamId(),
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            $stream->markAsResizingVideoFailed();
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->publishService->refreshStream($stream, ResizeVideoCommand::class);
            $this->publishService->refreshSearchStreams($stream, ResizeVideoCommand::class);
        }
    }
}
