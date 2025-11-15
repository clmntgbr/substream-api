<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Trait\WorkflowTrait;
use App\Domain\Stream\Repository\StreamRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class StreamFailureCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(StreamFailureCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => StreamFailureCommand::class,
            ]);

            return;
        }

        $this->mercurePublisher->refreshStreams($stream->getUser(), StreamFailureCommand::class);
    }
}
