<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\GenerateSubtitleCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('extractsoundsuccess')]
final class ExtractSoundSuccessWebhookConsumer implements ConsumerInterface
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
            $stream->setAudioFiles($response->getAudioFiles());
            $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND_COMPLETED);

            $this->commandBus->dispatch(new GenerateSubtitleCommand(
                streamId: $stream->getId(),
                audioFiles: $stream->getAudioFiles(),
            ));
        } catch (\Exception $e) {
            $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
