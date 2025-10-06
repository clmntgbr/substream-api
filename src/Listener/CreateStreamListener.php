<?php

namespace App\Listener;

use App\Event\CreateStreamEvent;
use App\Repository\StreamRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: CreateStreamEvent::class, method: 'onCreateStream')]
class CreateStreamListener
{
    public function __construct(
        private StreamRepository $streamRepository,
    ) {
    }

    public function onCreateStream(CreateStreamEvent $event): void
    {
        $streamId = $event->getStreamId();

        $stream = $this->streamRepository->findByUuid($streamId);

        if (null === $stream) {
            return;
        }
    }
}
