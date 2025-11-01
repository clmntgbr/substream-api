<?php

declare(strict_types=1);

namespace App\Dto\OAuth\LinkedIn;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class LinkedInAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[SerializedName('given_name')]
    #[Assert\Type('string')]
    public ?string $firstName;

    #[SerializedName('family_name')]
    #[Assert\Type('string')]
    public ?string $lastName;

    #[Assert\Type('string')]
    public ?string $email;

    #[SerializedName('picture')]
    public ?string $profilePicture;

    public static function fromArray(array $data): self
    {
        $account = new self();
        $account->id = (string) $data['sub'];
        $account->firstName = $data['given_name'] ?? null;
        $account->lastName = $data['family_name'] ?? null;
        $account->email = $data['email'];
        $account->profilePicture = $data['picture'] ?? null;

        return $account;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function getName(): string
    {
        return trim(($this->firstName ?? '').' '.($this->lastName ?? ''));
    }

    public function getId(): string
    {
        return $this->id;
    }
}
