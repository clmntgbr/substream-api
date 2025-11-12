<?php

declare(strict_types=1);

namespace App\CoreDD\Application\SocialAccount\Command;

use App\CoreDD\Domain\User\Entity\User;
use App\Shared\Application\Command\SynchronousInterface;

final class CreateSocialAccountCommand implements SynchronousInterface
{
    public function __construct(
        public string $provider,
        public string $accountId,
        public string $email,
        public User $user,
    ) {
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
