<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Stream\Command;

use App\CoreDD\Domain\Stream\Repository\StreamRepository;
use App\CoreDD\Infrastructure\Storage\S3\S3StorageService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CleanStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
        private S3StorageService $s3Service,
    ) {
    }

    public function __invoke(CleanStreamCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => CleanStreamCommand::class,
            ]);

            return;
        }

        foreach ($stream->getCleanableFiles() as $cleanableFile) {
            $this->s3Service->delete($stream->getId(), $cleanableFile);
        }
    }
}
