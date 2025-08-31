<?php

namespace App\Application\CommandHandler;

use App\Application\Command\UploadVideoByUrlCommand;
use App\Service\UploadVideoServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UploadVideoByUrlCommandHandler
{
    public function __construct(
    ) {
    }

    public function __invoke(UploadVideoByUrlCommand $command): void
    {
        
    }
}
