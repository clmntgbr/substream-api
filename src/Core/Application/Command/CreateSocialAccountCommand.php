<?php

namespace App\Core\Application\Command;

use App\Entity\User;
use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateSocialAccountCommand extends CommandAbstract implements SyncCommandInterface
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
