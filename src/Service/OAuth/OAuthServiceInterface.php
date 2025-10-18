<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\ExchangeTokenPayloadInterface;

interface OAuthServiceInterface
{
    public const TWITTER_CALLBACK_URL = '/api/oauth/twitter/callback';

    public function connect(): string;

    public function callback(ExchangeTokenPayloadInterface $payload): void;
}
