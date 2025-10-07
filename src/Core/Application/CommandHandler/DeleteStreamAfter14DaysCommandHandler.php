<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\DeleteStreamAfter14DaysCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CoreBusInterface;
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
        private CoreBusInterface $coreBus,
        private TaskRepository $taskRepository,
        private UploadFileServiceInterface $uploadFileService,
    ) {
    }

    public function __invoke(DeleteStreamAfter14DaysCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->uploadFileService->deleteAllFiles($stream->getId());

        $stream->markAsDeleted();
        $this->streamRepository->save($stream);
    }
}
