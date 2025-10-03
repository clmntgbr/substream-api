<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\GenerateSubtitleSuccess;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GenerateSubtitleSuccess $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $stream->setSubtitleSrtFileName($response->getSubtitleSrtFileName());
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_COMPLETED);

            $this->commandBus->dispatch(new TransformSubtitleCommand(
                streamId: $stream->getId(),
                subtitleSrtFileName: $stream->getSubtitleSrtFileName(),
            ));
        } catch (\Exception $e) {
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_FAILED);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
