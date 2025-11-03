<?php

declare(strict_types=1);

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
        if (null === $this->code) {
            throw new \RuntimeException('Code is required');
        }

        return $this->code;
    }

    public function getState(): string
    {
        if (null === $this->state) {
            throw new \RuntimeException('State is required');
        }

        return $this->state;
    }

    public function getCodeVerifier(): string
    {
        if (null === $this->codeVerifier) {
            throw new \RuntimeException('Code verifier is required');
        }

        return $this->codeVerifier;
    }
}
