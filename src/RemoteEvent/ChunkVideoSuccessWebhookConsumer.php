<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\CompleteVideoCommand;
use App\Core\Application\Command\ResumeVideoCommand;
use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\ChunkVideoSuccess;
use App\Entity\Stream;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
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

            $this->dispatch($stream);
        } catch (\Exception $e) {
            $stream->markAsChunkingVideoFailed();
        } finally {
            $this->streamRepository->save($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }

    private function dispatch(Stream $stream): void
    {
        if (true === $stream->getOption()->getIsResume()) {
            $this->commandBus->dispatch(new ResumeVideoCommand(
                streamId: $stream->getId(),
                subtitleSrtFileName: $stream->getSubtitleSrtFileName(),
            ));
        }

        if (false === $stream->getOption()->getIsResume()) {
            $this->commandBus->dispatch(new CompleteVideoCommand(
                streamId: $stream->getId(),
            ));
        }
    }
}
