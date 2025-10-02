<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoCommand;
use App\Core\Application\Message\GetVideoMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class GetVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private WorkflowInterface $streamsStateMachine,
        private CoreBusInterface $coreBus,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(GetVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->coreBus->dispatch(new GetVideoMessage(
            streamId: $stream->getId(),
            url: $command->getUrl(),
        ));
    }
}
