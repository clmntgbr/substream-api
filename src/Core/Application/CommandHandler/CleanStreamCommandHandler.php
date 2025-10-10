<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CleanStreamCommand;
use App\Repository\StreamRepository;
use App\Service\S3ServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CleanStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private LoggerInterface $logger,
        private S3ServiceInterface $s3Service,
    ) {
    }

    public function __invoke(CleanStreamCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        foreach ($stream->getCleanableFiles() as $cleanableFile) {
            $this->s3Service->delete($stream->getId(), $cleanableFile);
        }
    }
}
