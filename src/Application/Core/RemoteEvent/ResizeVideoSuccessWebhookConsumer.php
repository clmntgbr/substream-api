<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Core\Command\EmbedVideoCommand;
use App\Application\Task\Command\UpdateTaskSuccessCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\ResizeVideoSuccess;
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

#[AsRemoteEventConsumer('resizevideosuccess')]
final class ResizeVideoSuccessWebhookConsumer implements ConsumerInterface
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
        /** @var ResizeVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setResizeFileName($response->getResizeFileName());
            $this->apply($stream, WorkflowTransitionEnum::RESIZING_VIDEO_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $subtitleAssFileName = $stream->getSubtitleAssFileName();
            $resizeFileName = $stream->getResizeFileName();

            if (null === $subtitleAssFileName) {
                throw new RuntimeException('Subtitle ASS file name is required');
            }

            if (null === $resizeFileName) {
                throw new RuntimeException('Resize file name is required');
            }

            $this->commandBus->dispatch(new EmbedVideoCommand(
                streamId: $stream->getId(),
                subtitleAssFileName: $subtitleAssFileName,
                resizeFileName: $resizeFileName,
            ));
        } catch (Exception $e) {
            $this->logger->error('Error resizing video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::RESIZING_VIDEO_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
