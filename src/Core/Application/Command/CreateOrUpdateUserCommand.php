<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateOrUpdateUserCommand extends CommandAbstract implements SyncCommandInterface
{
    public function __construct(
        public string $firstname,
        public string $lastname,
        public string $email,
    ) {
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
