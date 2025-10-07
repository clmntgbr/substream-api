<?php

namespace App\RemoteEvent;

use App\Core\Application\Command\UpdateTaskCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\Webhook\GetVideoFailure;
use App\Enum\TaskStatusEnum;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
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
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function consume(RemoteEvent $event): void
    {
        /** @var GetVideoFailure $response */
        $response = $event->getPayload()['payload'];

        $stream = $this->streamRepository->findByUuid($response->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $response->getStreamId(),
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::UPLOAD_FAILED);
        } catch (\Exception $e) {
            $stream->markAsUploadFailed();
        } finally {
            $this->streamRepository->save($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskCommand(
            taskId: $response->getTaskId(),
            processingTime: 0,
            taskStatus: TaskStatusEnum::FAILED,
        ));
    }
}
