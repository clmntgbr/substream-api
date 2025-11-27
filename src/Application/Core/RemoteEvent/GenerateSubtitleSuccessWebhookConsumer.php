<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Core\Command\TransformSubtitleCommand;
use App\Application\Task\Command\UpdateTaskSuccessCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\GenerateSubtitleSuccess;
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

#[AsRemoteEventConsumer('generatesubtitlesuccess')]
final class GenerateSubtitleSuccessWebhookConsumer implements ConsumerInterface
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
        /** @var GenerateSubtitleSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setSubtitleSrtFileName($response->getSubtitleSrtFileName());
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $subtitleSrtFileName = $stream->getSubtitleSrtFileName();
            if (null === $subtitleSrtFileName) {
                throw new RuntimeException('Subtitle SRT file name is required');
            }

            $this->commandBus->dispatch(new TransformSubtitleCommand(
                streamId: $stream->getId(),
                subtitleSrtFileName: $subtitleSrtFileName,
            ));
        } catch (Exception $e) {
            $this->logger->error('Error generating subtitle', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::GENERATING_SUBTITLE_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
