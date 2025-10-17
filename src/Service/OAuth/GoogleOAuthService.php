<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\CallbackPayloadInterface;
use App\Entity\User;

class GoogleOAuthService implements OAuthServiceInterface
{
    public function connect(): string
    {
        return 'connected';
    }

    public function getScopes(): array
    {
        return [
            'openid',
            'email',
            'profile',
            'https://www.googleapis.com/auth/userinfo.email',
            'https://www.googleapis.com/auth/userinfo.profile',
            'https://www.googleapis.com/auth/userinfo.phone',
            'https://www.googleapis.com/auth/userinfo.address',
            'https://www.googleapis.com/auth/userinfo.birthday',
            'https://www.googleapis.com/auth/userinfo.gender',
            'https://www.googleapis.com/auth/userinfo.locale',
            'https://www.googleapis.com/auth/userinfo.timezone',
            'https://www.googleapis.com/auth/userinfo.language',
        ];
    }

    public function callback(CallbackPayloadInterface $payload): void
    {
        dd($payload);
    }
}