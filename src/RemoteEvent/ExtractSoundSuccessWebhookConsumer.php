<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\GenerateSubtitleCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
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
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GetVideoSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->setAudioFiles($response->getAudioFiles());

        $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND_COMPLETED);
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new GenerateSubtitleCommand(
            streamId: $stream->getId(),
            audioFiles: $stream->getAudioFiles(),
        ));
    }
}
