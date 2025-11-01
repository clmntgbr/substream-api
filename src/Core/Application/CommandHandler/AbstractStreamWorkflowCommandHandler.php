<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Entity\Stream;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\Exception\TransitionException as WorkflowTransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractStreamWorkflowCommandHandler
{
    public function __construct(
        private readonly StreamRepository $streamRepository,
        private readonly WorkflowInterface $streamsStateMachine,
        private readonly LoggerInterface $logger,
        private readonly CoreBusInterface $coreBus,
        private readonly TaskRepository $taskRepository,
        private readonly PublishServiceInterface $publishService,
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

    protected function executeWorkflow(Uuid $streamId, ?callable $messageBuilder = null): void
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

            if (null !== $messageBuilder) {
                $message = $messageBuilder($stream, $task);
            } else {
                $message = $this->createMessage($stream, $task);
            }

            $this->coreBus->dispatch($message);
        } catch (WorkflowTransitionException $e) {
            $this->logger->error(sprintf('Workflow transition failed during %s', $this->getWorkflowActionName()), [
                'stream_id' => (string) $streamId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->markStreamAsFailed($stream);
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Throwable $e) {
            $this->logger->error(sprintf('Unexpected error during %s', $this->getWorkflowActionName()), [
                'stream_id' => (string) $streamId,
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->markStreamAsFailed($stream);
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->publishService->refreshStream($stream, $this->getCommandClass());
            $this->publishService->refreshSearchStreams($stream, $this->getCommandClass());
        }
    }
}
