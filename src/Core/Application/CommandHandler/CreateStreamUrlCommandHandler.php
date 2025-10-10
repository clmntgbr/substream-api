<?php

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Command\CreateStreamUrlCommand;
use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Enum\WorkflowTransitionEnum;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CreateStreamUrlCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private ValidatorInterface $validator,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(CreateStreamUrlCommand $command): CreateStreamModel
    {
        $createStreamModel = $this->commandBus->dispatch(new CreateStreamCommand(
            user: $command->getUser(),
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
            optionId: $command->getOptionId(),
        ));

        $stream = $this->streamRepository->find($command->getStreamId());

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $this->apply($stream, WorkflowTransitionEnum::UPLOADING);
        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new GetVideoCommand(
            streamId: $command->getStreamId(),
            url: $command->getUrl(),
        ));

        return $createStreamModel;
    }
}
