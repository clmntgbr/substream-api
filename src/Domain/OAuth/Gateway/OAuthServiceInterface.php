<?php

declare(strict_types=1);

namespace App\Domain\OAuth\Gateway;

use App\Domain\OAuth\Dto\ExchangeTokenPayloadInterface;
use App\Domain\User\Entity\User;

interface OAuthServiceInterface
{
    /**
     * @return array<string, mixed>
     */
    public function connect(): array;

    public function callback(ExchangeTokenPayloadInterface $payload): User;
}
