<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UploadVideoCommand;
use App\Service\UploadVideoServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UploadVideoCommandHandler
{
    public function __construct(
        private UploadVideoServiceInterface $uploadVideoService,
    ) {
    }

    public function __invoke(UploadVideoCommand $command): void
    {
        $this->uploadVideoService->upload($command->file, $command->userId, $command->streamId);
    }
}
