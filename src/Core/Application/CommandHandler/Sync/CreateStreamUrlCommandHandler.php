<?php

namespace App\Core\Application\CommandHandler\Sync;

use App\Core\Application\Command\Async\GetVideoCommand;
use App\Core\Application\Command\Sync\CreateStreamCommand;
use App\Core\Application\Command\Sync\CreateStreamUrlCommand;
use App\Core\Domain\Aggregate\CreateStreamModel;
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

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
        ));

        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            user: $command->getUser(),
            url: $command->getUrl(),
        ));

        return $createStreamModel;
    }
}
