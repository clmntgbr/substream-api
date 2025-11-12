<?php

declare(strict_types=1);

namespace App\CoreDD\Domain\OAuth\Gateway;

use App\CoreDD\Domain\User\Entity\User;
use App\Dto\OAuth\ExchangeTokenPayloadInterface;

interface OAuthServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function connect(): array;

    public function callback(ExchangeTokenPayloadInterface $payload): User;
}
