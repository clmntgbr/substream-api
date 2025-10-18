<?php

namespace App\Dto\OAuth\Github;

use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class GithubExchangeTokenPayload implements ExchangeTokenPayloadInterface
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $code = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $state = null;

    #[SerializedName('code_verifier')]
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $codeVerifier = null;

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
