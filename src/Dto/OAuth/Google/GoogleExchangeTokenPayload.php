<?php

declare(strict_types=1);

namespace App\Dto\OAuth\Google;

use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GoogleExchangeTokenPayload implements ExchangeTokenPayloadInterface
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
