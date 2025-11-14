<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Core\Command\ResumeVideoCommand;
use App\Application\Stream\Command\StreamSuccessCommand;
use App\Application\Task\Command\UpdateTaskSuccessCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\ChunkVideoSuccess;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('chunkvideosuccess')]
final class ChunkVideoSuccessWebhookConsumer implements ConsumerInterface
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
        /** @var ChunkVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setChunkFileNames($response->getChunkFileNames());
            $this->apply($stream, WorkflowTransitionEnum::CHUNKING_VIDEO_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $this->dispatch($stream);
        } catch (\Exception $e) {
            $this->logger->error('Error chunking video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::CHUNKING_VIDEO_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }

    private function dispatch(Stream $stream): void
    {
        if (true === $stream->getOption()->getIsResume()) {
            $subtitleSrtFileName = $stream->getSubtitleSrtFileName();
            if (null === $subtitleSrtFileName) {
                throw new \RuntimeException('Subtitle SRT file name is required');
            }

            $this->commandBus->dispatch(new ResumeVideoCommand(
                streamId: $stream->getId(),
                subtitleSrtFileName: $subtitleSrtFileName,
            ));
        }

        if (false === $stream->getOption()->getIsResume()) {
            $this->commandBus->dispatch(new StreamSuccessCommand(
                streamId: $stream->getId(),
            ));
        }
    }
}
