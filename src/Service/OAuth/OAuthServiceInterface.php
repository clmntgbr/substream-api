<?php

namespace App\Service\OAuth;

use App\Entity\User;
use App\Dto\OAuth\CallbackPayloadInterface;

interface OAuthServiceInterface
{
    public const TWITTER_CALLBACK_URL = '/api/oauth/twitter/callback';

    public function connect(): string;
    public function getScopes(): array;
    public function callback(CallbackPayloadInterface $payload): void;
}