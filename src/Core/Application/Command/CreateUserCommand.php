<?php

namespace App\Core\Application\Command;

use App\Shared\Application\Command\CommandAbstract;
use App\Shared\Application\Command\SyncCommandInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserCommand extends CommandAbstract implements SyncCommandInterface
{
    public function __construct(
        #[Assert\NotBlank(message: 'First name is required')]
        #[Assert\Length(min: 3, max: 255, minMessage: 'First name must be at least {{ limit }} characters', maxMessage: 'First name cannot be longer than {{ limit }} characters')]
        public string $firstname,
        #[Assert\NotBlank(message: 'Last name is required')]
        #[Assert\Length(min: 3, max: 255, minMessage: 'Last name must be at least {{ limit }} characters', maxMessage: 'Last name cannot be longer than {{ limit }} characters')]
        public string $lastname,
        #[Assert\NotBlank(message: 'Email is required')]
        #[Assert\Email(message: 'Invalid email address')]
        public string $email,
        #[SerializedName('password')]
        #[Assert\NotBlank(message: 'Password is required')]
        #[Assert\Length(min: 8, max: 255, minMessage: 'Password must be at least {{ limit }} characters', maxMessage: 'Password cannot be longer than {{ limit }} characters')]
        public string $plainPassword,
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

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }
}
