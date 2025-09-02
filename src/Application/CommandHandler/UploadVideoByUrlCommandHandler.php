<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UploadVideoByUrlCommand;
use App\Client\Processor\GetVideoByUrlProcessor;
use App\Repository\StreamRepository;
use App\Service\UploadVideoServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UploadVideoByUrlCommandHandler
{
    public function __construct(
        private GetVideoByUrlProcessor $getVideoByUrlProcessor,
        private StreamRepository $streamRepository,
        private UploadVideoServiceInterface $uploadVideoService,
    ) {
    }

    public function __invoke(UploadVideoByUrlCommand $command): void
    {
        $this->uploadVideoService->uploadByUrl($command->url, $command->userId, $command->streamId);
    }
}
