<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\ChunkVideoCommand;
use App\Core\Application\Command\UpdateTaskCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\EmbedVideoSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('embedvideosuccess')]
final class EmbedVideoSuccessWebhookConsumer implements ConsumerInterface
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
        /** @var EmbedVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setEmbedFileName($response->getEmbedFileName());
            $this->apply($stream, WorkflowTransitionEnum::EMBEDDING_VIDEO_COMPLETED);

            $this->commandBus->dispatch(new UpdateTaskCommand(
                taskId: $response->getTaskId(),
                processingTime: $response->getProcessingTime(),
            ));

            $this->commandBus->dispatch(new ChunkVideoCommand(
                streamId: $stream->getId(),
                chunkNumber: 5,
                embedFileName: $stream->getEmbedFileName(),
            ));
        } catch (\Exception $e) {
            $stream->markAsEmbeddingVideoFailed();
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
