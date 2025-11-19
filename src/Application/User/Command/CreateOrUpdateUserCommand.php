<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Shared\Application\Command\SynchronousInterface;

final class CreateOrUpdateUserCommand implements SynchronousInterface
{
    public function __construct(
        public string $email,
        public ?string $firstname = null,
        public ?string $lastname = null,
        public ?string $picture = null,
    ) {
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
