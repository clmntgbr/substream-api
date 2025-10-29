<?php

namespace App\Dto\OAuth\Github;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class GithubAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    #[Assert\Type('string')]
    public ?string $email;

    #[SerializedName('avatar_url')]
    public ?string $avatarUrl;

    public static function fromArray(array $data): self
    {
        $account = new self();
        $account->name = $data['name'] ?? $data['login'];
        $account->id = (string) $data['id'];
        $account->email = $data['email'] ?? null;
        $account->avatarUrl = $data['avatar_url'] ?? null;

        return $account;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
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
