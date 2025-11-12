<?php

declare(strict_types=1);

namespace App\RemoteEvent;

use App\Core\Application\Command\UpdateTaskFailureCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Stream\Enum\StreamStatusEnum;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Dto\Webhook\ChunkVideoFailure;
use App\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('chunkvideofailure')]
final class ChunkVideoFailureWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var ChunkVideoFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::CHUNKING_VIDEO_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Exception $e) {
            $this->logger->error('Error chunking video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::CHUNKING_VIDEO_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskFailureCommand(
            taskId: $response->getTaskId(),
        ));
    }
}
