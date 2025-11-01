<?php

declare(strict_types=1);

namespace App\Service\OAuth;

use App\Dto\OAuth\ExchangeTokenPayloadInterface;
use App\Entity\User;

interface OAuthServiceInterface
{
    public function connect(): array;

    public function callback(ExchangeTokenPayloadInterface $payload): User;
}
