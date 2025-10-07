<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\EmbedVideoCommand;
use App\Core\Application\Command\UpdateTaskCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\ResizeVideoSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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

            $this->commandBus->dispatch(new EmbedVideoCommand(
                streamId: $stream->getId(),
                subtitleAssFileName: $stream->getSubtitleAssFileName(),
                resizeFileName: $stream->getResizeFileName(),
            ));
        } catch (\Exception $e) {
            $stream->markAsResizingVideoFailed();
        } finally {
            $this->streamRepository->save($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
