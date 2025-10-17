<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\CallbackPayloadInterface;
use App\Entity\User;

class FacebookOAuthService implements OAuthServiceInterface
{
    public function connect(): string
    {
        return 'connected';
    }

    public function getScopes(): array
    {
        return [
            'email',
            'pages_manage_cta',
            'pages_show_list',
            'read_page_mailboxes',
            'business_management',
            'pages_messaging',
            'pages_messaging_subscriptions',
            'page_events',
            'pages_read_engagement',
            'pages_manage_metadata',
            'pages_read_user_content',
            'pages_manage_ads',
            'pages_manage_posts',
            'pages_manage_engagement',
        ];
    }

    public function callback(CallbackPayloadInterface $payload): void
    {
        dd($payload);
    }
}