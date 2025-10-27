<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\GetVideoSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('getvideosuccess')]
final class GetVideoSuccessWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GetVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setFileName($response->getFileName());
            $stream->setOriginalFileName($response->getOriginalFileName());
            $stream->setMimeType($response->getMimeType());
            $stream->setSize($response->getSize());
            $this->apply($stream, WorkflowTransitionEnum::UPLOADED);
            $this->streamRepository->save($stream);

            $this->commandBus->dispatch(new ExtractSoundCommand(
                streamId: $stream->getId(),
                fileName: $stream->getFileName(),
            ));
        } catch (\Exception $e) {
            $this->logger->error('Error getting video', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsUploadFailed();
            $this->streamRepository->save($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $response->getTaskId(),
            processingTime: $response->getProcessingTime(),
        ));
    }
}
