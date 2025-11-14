<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Trait\WorkflowTrait;
use App\Domain\Stream\Repository\StreamRepository;
use App\Infrastructure\Storage\S3\S3StorageService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class DeleteStreamAfter14DaysCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private S3StorageService $s3Service,
    ) {
    }

    public function __invoke(DeleteStreamAfter14DaysCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => DeleteStreamAfter14DaysCommand::class,
            ]);

            return;
        }

        $this->s3Service->deleteAll($stream->getId());

        $stream->markAsDeleted();
        $this->streamRepository->saveAndFlush($stream);
    }
}
