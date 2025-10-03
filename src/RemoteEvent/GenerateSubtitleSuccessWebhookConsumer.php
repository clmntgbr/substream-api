<?php

namespace App\RemoteEvent;

use App\Dto\Webhook\GenerateSubtitleSuccess;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
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
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GenerateSubtitleSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->setSubtitle($response->getSubtitle());

        $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_COMPLETED);
        $this->streamRepository->save($stream);

        // $this->commandBus->dispatch(new GenerateSubtitleCommand(
        //     streamId: $stream->getId(),
        //     audioFiles: $stream->getAudioFiles(),
        // ));
    }
}
