<?php

declare(strict_types=1);

namespace App\Infrastructure\OAuth\Listener;

use App\Domain\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Psr\Log\LoggerInterface;

final class JWTCreatedListener
{
    public function __construct(
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();

        $payload['id'] = (string) $user->getId();
        $payload['email'] = $user->getEmail();
        $payload['firstname'] = $user->getFirstname();
        $payload['lastname'] = $user->getLastname();
        $payload['picture'] = $user->getPicture();

        if (null !== $this->logger) {
            $this->logger->info('JWT Created with custom claims', [
                'user_id' => (string) $user->getId(),
                'payload_keys' => array_keys($payload),
            ]);
        }

        $event->setData($payload);
    }
}
