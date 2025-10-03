<?php

namespace App\RemoteEvent;

use App\Dto\Webhook\GenerateSubtitleFailure;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('generatesubtitlefailure')]
final class GenerateSubtitleFailureWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;
    
    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GenerateSubtitleFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_FAILED);
        $this->streamRepository->save($stream);
    }
}
