<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Application\Bus\CoreBusInterface;
use App\Shared\Application\Message\AsyncMessageInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class CoreBus implements CoreBusInterface
{
    public function __construct(
        private MessageBusInterface $coreBus,
    ) {
    }

    public function dispatch(object $message): Envelope
    {
        if (!$message instanceof AsyncMessageInterface) {
            throw new \RuntimeException('The message must implement AsyncMessageInterface.');
        }

        return $this->coreBus->dispatch($message, [$message->getRoutingKey()]);
    }
}
