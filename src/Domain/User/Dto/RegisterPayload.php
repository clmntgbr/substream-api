<?php

declare(strict_types=1);

namespace App\Domain\User\Dto;

use App\Infrastructure\Validation\Constraint\UniqueEmail;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterPayload
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
        #[UniqueEmail]
        public string $email,
        #[Assert\NotBlank(message: 'Confirm password is required')]
        #[Assert\EqualTo(propertyPath: 'plainPassword', message: 'Passwords do not match')]
        public string $confirmPassword,
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

    public function getConfirmPassword(): string
    {
        return $this->confirmPassword;
    }
}
