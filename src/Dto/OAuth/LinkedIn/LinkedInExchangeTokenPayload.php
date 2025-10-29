<?php

namespace App\Dto\OAuth\LinkedIn;

use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LinkedInExchangeTokenPayload implements ExchangeTokenPayloadInterface
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
