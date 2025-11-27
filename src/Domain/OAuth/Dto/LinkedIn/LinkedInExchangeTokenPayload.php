<?php

declare(strict_types=1);

namespace App\Domain\OAuth\Dto\LinkedIn;

use App\Domain\OAuth\Dto\ExchangeTokenPayloadInterface;
use RuntimeException;
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
        if (null === $this->code) {
            throw new RuntimeException('Code is required');
        }

        return $this->code;
    }

    public function getState(): string
    {
        if (null === $this->state) {
            throw new RuntimeException('State is required');
        }

        return $this->state;
    }
}
