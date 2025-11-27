<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Core\Command\ChunkVideoCommand;
use App\Application\Task\Command\UpdateTaskSuccessCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\EmbedVideoSuccess;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Exception;
use Psr\Log\LoggerInterface;
use RuntimeException;
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
            $this->streamRepository->saveAndFlush($stream);

            $embedFileName = $stream->getEmbedFileName();
            if (null === $embedFileName) {
                throw new RuntimeException('Embed file name is required');
            }

            $this->commandBus->dispatch(new ChunkVideoCommand(
                streamId: $stream->getId(),
                embedFileName: $embedFileName,
            ));
        } catch (Exception $e) {
            $this->logger->error('Error embedding video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::EMBEDDING_VIDEO_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
