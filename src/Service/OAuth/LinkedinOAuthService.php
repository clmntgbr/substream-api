<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\CallbackPayloadInterface;

class LinkedinOAuthService implements OAuthServiceInterface
{
    public function connect(): string
    {
        return 'connected';
    }

    public function getScopes(): array
    {
        return [
            'profile',
            'email,openid',
            'w_member_social',
        ];
    }

    public function callback(CallbackPayloadInterface $payload): void
    {
        dd($payload);
    }
}
