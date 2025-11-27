<?php

declare(strict_types=1);

namespace App\Infrastructure\User\Listener;

use App\Domain\User\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
#[AsDoctrineListener(event: Events::postPersist)]
final class UserListener
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public function prePersist(PrePersistEventArgs $prePersistEventArgs): void
    {
        $entity = $prePersistEventArgs->getObject();
        if (! $entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    public function postPersist(PostPersistEventArgs $postPersistEventArgs): void
    {
        $entity = $postPersistEventArgs->getObject();
        if (! $entity instanceof User) {
            return;
        }
    }

    public function preUpdate(PreUpdateEventArgs $preUpdateEventArgs): void
    {
        $entity = $preUpdateEventArgs->getObject();
        if (! $entity instanceof User) {
            return;
        }

        $this->hashPassword($entity);
    }

    private function hashPassword(User $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $password = $this->userPasswordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPassword($password);
        }

        $user->eraseCredentials();
    }
}
