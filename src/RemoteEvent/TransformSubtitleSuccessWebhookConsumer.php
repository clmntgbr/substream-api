<?php

declare(strict_types=1);

namespace App\RemoteEvent;

use App\Core\Application\Command\ResizeVideoCommand;
use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\TransformSubtitleSuccess;
use App\Enum\StreamStatusEnum;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('transformsubtitlesuccess')]
final class TransformSubtitleSuccessWebhookConsumer implements ConsumerInterface
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
        /** @var TransformSubtitleSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setSubtitleAssFileName($response->getSubtitleAssFileName());
            $this->apply($stream, WorkflowTransitionEnum::TRANSFORMING_SUBTITLE_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $this->commandBus->dispatch(new ResizeVideoCommand(
                streamId: $stream->getId(),
                fileName: $stream->getFileName(),
            ));
        } catch (\Exception $e) {
            $this->logger->error('Error transforming subtitle', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
