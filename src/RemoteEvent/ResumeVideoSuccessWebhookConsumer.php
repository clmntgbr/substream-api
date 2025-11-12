<?php

declare(strict_types=1);

namespace App\RemoteEvent;

use App\Core\Application\Command\StreamSuccessCommand;
use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Stream\Enum\StreamStatusEnum;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Dto\Webhook\ResumeVideoSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('resumevideosuccess')]
final class ResumeVideoSuccessWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var ResumeVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setResumeFileName($response->getResumeFileName());
            $this->apply($stream, WorkflowTransitionEnum::RESUMING_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $this->commandBus->dispatch(new StreamSuccessCommand(
                streamId: $stream->getId(),
            ));
        } catch (\Exception $e) {
            $this->logger->error('Error resuming video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::RESUMING_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
