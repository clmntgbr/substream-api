<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Trait\JobTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Repository\JobRepository;
use App\Service\JobContextService;
use App\Service\UploadFileServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler
{
    use JobTrait;

    public function __construct(
        private UploadFileServiceInterface $uploadFileService,
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            url: $command->url,
        ));

        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            user: $command->getUser(),
            url: $command->url,
        ));

        $this->markJobAsSuccess();

        return $createStreamModel;
    }
}
