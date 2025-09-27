<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler
{
    public function __construct(
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateStreamUrlCommand $command): void
    {
    }
}
