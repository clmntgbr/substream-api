<?php

namespace App\RemoteEvent;

use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\ExtractSoundFailure;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('extractsoundfailure')]
final class ExtractSoundFailureWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var ExtractSoundFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND_FAILED);
        $this->streamRepository->save($stream);
    }
}
