<?php

namespace App\Dto\OAuth;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

class TwitterCallbackPayload implements CallbackPayloadInterface
{
    #[Assert\Type('string')]
    #[SerializedName('oauth_token')]
    #[Assert\NotBlank()]
    public ?string $oauthToken = null;

    #[Assert\Type('string')]
    #[SerializedName('oauth_verifier')]
    #[Assert\NotBlank()]
    public ?string $oauthVerifier = null;

    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $state = null;

    public function getOauthToken(): string
    {
        return $this->oauthToken;
    }

    public function getOauthVerifier(): string
    {
        return $this->oauthVerifier;
    }

    public function getState(): string
    {
        return $this->state;
    }
}