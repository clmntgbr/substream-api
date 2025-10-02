<?php

namespace App\RemoteEvent;

use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\GetVideoFailure;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('getvideofailure')]
final class GetVideoFailureWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GetVideoFailure $response */
        $response = $this->denormalizer->denormalize($event->getPayload(), GetVideoFailure::class);

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->apply($stream, WorkflowTransitionEnum::UPLOAD_FAILED);
        $this->streamRepository->save($stream);
    }
}
