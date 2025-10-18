<?php

namespace App\Dto\OAuth;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterExchangeTokenPayload
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $code = null;

    #[Assert\Type('string')]
    #[SerializedName('code_verifier')]
    #[Assert\NotBlank()]
    public ?string $codeVerifier = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $state = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCodeVerifier(): string
    {
        return $this->codeVerifier;
    }
}