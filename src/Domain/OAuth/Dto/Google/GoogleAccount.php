<?php

declare(strict_types=1);

namespace App\Domain\OAuth\Dto\Google;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class GoogleAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[SerializedName('sub')]
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('given_name')]
    public string $givenName;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[SerializedName('family_name')]
    public string $familyName;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $email;

    #[SerializedName('picture')]
    public ?string $picture;

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $account = new self();
        $account->name = $data['name'];
        $account->id = $data['sub'];
        $account->givenName = $data['given_name'];
        $account->familyName = $data['family_name'];
        $account->email = $data['email'];
        $account->picture = $data['picture'] ?? null;

        return $account;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }
}
