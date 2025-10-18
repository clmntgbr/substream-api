<?php

namespace App\Dto\OAuth;

use Symfony\Component\Validator\Constraints as Assert;

class GoogleExchangeTokenPayload implements CallbackPayloadInterface
{
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    public ?string $code = null;

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
}
