<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\GetVideoByUrlCommand;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Repository\JobRepository;
use App\Service\JobContextService;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
    }

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $streamId = Uuid::v4();

        $this->commandBus->dispatch(new GetVideoByUrlCommand(
            streamId: $streamId,
            user: $command->user,
            url: $command->url,
        ));

        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->user,
            streamId: $streamId,
            url: $command->url,
        ));

        return $createStreamModel;
    }
}
