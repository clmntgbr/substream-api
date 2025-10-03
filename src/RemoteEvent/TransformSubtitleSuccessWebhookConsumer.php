<?php

namespace App\RemoteEvent;

use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\TransformSubtitleSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsRemoteEventConsumer('transformsubtitlesuccess')]
final class TransformSubtitleSuccessWebhookConsumer implements ConsumerInterface
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var TransformSubtitleSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setSubtitleAssFileName($response->getSubtitleAssFileName());
            $this->apply($stream, WorkflowTransitionEnum::TRANSFORMING_SUBTITLE_COMPLETED);
        } catch (\Exception $e) {
            $this->apply($stream, WorkflowTransitionEnum::TRANSFORMING_SUBTITLE_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
