<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Core\Command\ResizeVideoCommand;
use App\Application\Task\Command\UpdateTaskSuccessCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\TransformSubtitleSuccess;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
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

            $fileName = $stream->getFileName();
            if (null === $fileName) {
                throw new \RuntimeException('File name is required');
            }

            $this->commandBus->dispatch(new ResizeVideoCommand(
                streamId: $stream->getId(),
                fileName: $fileName,
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
