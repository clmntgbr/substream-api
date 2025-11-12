<?php

declare(strict_types=1);

namespace App\Listener;

use App\CoreDD\Domain\User\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;
use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{
    public function __construct(
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();
        $data = $event->getData();

        $context = (new ObjectNormalizerContextBuilder())
            ->withGroups(User::GROUP_USER_READ)
            ->toArray();

        $data['user'] = json_decode($this->serializer->serialize($user, 'json', $context));
        $event->setData($data);
    }
}
