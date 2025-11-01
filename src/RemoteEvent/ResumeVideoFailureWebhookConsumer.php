<?php

declare(strict_types=1);

namespace App\RemoteEvent;

use App\Core\Application\Command\StreamSuccessCommand;
use App\Core\Application\Command\UpdateTaskFailureCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\ResumeVideoFailure;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('resumevideofailure')]
final class ResumeVideoFailureWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
        private TaskRepository $taskRepository,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var ResumeVideoFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::RESUMING_FAILED);
            $this->streamRepository->saveAndFlush($stream);

            $this->commandBus->dispatch(new StreamSuccessCommand(
                streamId: $stream->getId(),
            ));
        } catch (\Exception $e) {
            $this->logger->error('Error resuming video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsResumingFailed();
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskFailureCommand(
            taskId: $response->getTaskId(),
        ));
    }
}
