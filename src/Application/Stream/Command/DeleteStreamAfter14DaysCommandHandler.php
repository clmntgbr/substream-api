<?php

declare(strict_types=1);

namespace App\Application\Stream\Command;

use App\Application\Trait\WorkflowTrait;
use App\Shared\Application\Bus\CommandBusInterface as BusCommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteStreamAfter14DaysCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private BusCommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(DeleteStreamAfter14DaysCommand $command): void
    {
        $this->commandBus->dispatch(new DeleteStreamCommand($command->getStreamId()));
    }
}
