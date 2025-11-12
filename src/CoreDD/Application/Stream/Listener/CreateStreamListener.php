<?php

declare(strict_types=1);

namespace App\CoreDD\Application\Stream\Listener;

use App\CoreDD\Application\Stream\Command\DeleteStreamAfter14DaysCommand;
use App\CoreDD\Application\Stream\Event\CreateStreamEvent;
use App\CoreDD\Domain\Stream\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: CreateStreamEvent::class, method: 'onCreateStream')]
class CreateStreamListener
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function onCreateStream(CreateStreamEvent $event): void
    {
        $streamId = $event->getStreamId();

        $stream = $this->streamRepository->findByUuid($streamId);

        if (null === $stream) {
            return;
        }

        $this->commandBus->dispatch(new DeleteStreamAfter14DaysCommand(
            streamId: $streamId,
        ));
    }
}
