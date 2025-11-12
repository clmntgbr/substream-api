<?php

declare(strict_types=1);

namespace App\Core\Application\Stream\CommandHandler;

use App\Core\Application\Stream\Command\StreamFailureCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Stream\Repository\StreamRepository;
use App\Service\PublishServiceInterface;
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
        private PublishServiceInterface $publishService,
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

        $this->publishService->refreshStream($stream, StreamFailureCommand::class);
        $this->publishService->refreshSearchStreams($stream, StreamFailureCommand::class);
    }
}
