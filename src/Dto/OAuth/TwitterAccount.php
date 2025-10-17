<?php

namespace App\Dto\OAuth;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterAccount
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $username;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public string $id;

    public bool $verified;

    #[SerializedName('profile_image_url')]
    public ?string $profileImageUrl;
}