<?php

declare(strict_types=1);

namespace App\Application\Core\RemoteEvent;

use App\Application\Command\UpdateTaskFailureCommand;
use App\Application\Trait\WorkflowTrait;
use App\Domain\Core\Dto\ExtractSoundFailure;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Workflow\Enum\WorkflowTransitionEnum;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var ExtractSoundFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::EXTRACTING_SOUND_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Exception $e) {
            $this->logger->error('Error extracting sound', [
                'stream_id' => $response->getStreamId(),
                'error' => $e->getMessage(),
            ]);
            $stream->markAsFailed(StreamStatusEnum::EXTRACTING_SOUND_FAILED);
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskFailureCommand(
            taskId: $response->getTaskId(),
        ));
    }
}
