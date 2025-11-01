<?php

declare(strict_types=1);

namespace App\RemoteEvent;

use App\Core\Application\Command\UpdateTaskFailureCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\GenerateSubtitleFailure;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GenerateSubtitleFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_FAILED);
            $this->streamRepository->save($stream);
        } catch (\Exception $e) {
            $this->logger->error('Error generating subtitle', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsGenerateSubtitleFailed();
            $this->streamRepository->save($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskFailureCommand(
            taskId: $response->getTaskId(),
        ));
    }
}
