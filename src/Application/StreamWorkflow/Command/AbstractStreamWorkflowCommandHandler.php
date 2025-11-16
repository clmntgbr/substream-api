<?php

declare(strict_types=1);

namespace App\Application\StreamWorkflow\Command;

use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Task\Entity\Task;
use App\Domain\Task\Repository\TaskRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractStreamWorkflowCommandHandler
{
    public function __construct(
        private readonly StreamRepository $streamRepository,
        private readonly WorkflowInterface $streamsStateMachine,
        private readonly LoggerInterface $logger,
        private readonly CoreBusInterface $coreBus,
        private readonly TaskRepository $taskRepository,
        private readonly MercurePublisherInterface $mercurePublisher,
    ) {
    }

    protected function canApply(Stream $stream, WorkflowTransitionEnum $transition): bool
    {
        return $this->streamsStateMachine->can($stream, $transition->value);
    }

    protected function apply(Stream $stream, WorkflowTransitionEnum $transition): void
    {
        if (!$this->canApply($stream, $transition)) {
            throw new \InvalidArgumentException(sprintf('Transition "%s" cannot be applied to stream "%s"', $transition->value, $stream->getId()));
        }

        $this->streamsStateMachine->apply($stream, $transition->value);
    }

    abstract protected function getTransition(): WorkflowTransitionEnum;

    abstract protected function createMessage(Stream $stream, Task $task): object;

    abstract protected function markStreamAsFailed(Stream $stream): void;

    abstract protected function getCommandClass(): string;

    abstract protected function getWorkflowActionName(): string;

    protected function findStreamOrFail(Uuid $streamId): ?Stream
    {
        $stream = $this->streamRepository->findByUuid($streamId);

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $streamId,
                'command' => $this->getCommandClass(),
            ]);
        }

        return $stream;
    }

    protected function executeWorkflow(Uuid $streamId): void
    {
        $stream = $this->findStreamOrFail($streamId);

        if (null === $stream) {
            return;
        }

        try {
            $this->apply($stream, $this->getTransition());
            $this->streamRepository->saveAndFlush($stream);

            $task = Task::create($this->getCommandClass(), $stream);
            $this->taskRepository->saveAndFlush($task);

            $taskId = $task->getId();
            if (null === $taskId) {
                throw new \RuntimeException('Task ID is required');
            }

            $message = $this->createMessage($stream, $task);
            $this->coreBus->dispatch($message);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Workflow transition failed during %s', $this->getWorkflowActionName()), [
                'stream_id' => (string) $streamId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            dd($e->getMessage());
            $this->markStreamAsFailed($stream);
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Unexpected error during %s', $this->getWorkflowActionName()), [
                'stream_id' => (string) $streamId,
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            dd($e->getMessage());
            $this->markStreamAsFailed($stream);
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->mercurePublisher->refreshStreams($stream->getUser(), $this->getCommandClass());
        }
    }
}
