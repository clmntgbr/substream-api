<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CleanStreamCommand;
use App\Core\Application\Command\CompleteVideoCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class CompleteVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CompleteVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        if (false === $stream->getOption()->getIsResume()) {
            $this->apply($stream, WorkflowTransitionEnum::COMPLETED_NO_RESUME);
        }

        if (true === $stream->getOption()->getIsResume()) {
            $this->apply($stream, WorkflowTransitionEnum::COMPLETED);
        }

        $this->streamRepository->save($stream);

        $this->commandBus->dispatch(new CleanStreamCommand(
            streamId: $stream->getId(),
        ));
    }
}
