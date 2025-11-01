<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class OauthException extends BusinessException
{
    public function __construct(
        string $englishMessage = 'OAuth authentication failed',
        string $translationKey = 'error.oauth.failed',
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_UNAUTHORIZED);
    }

    public static function providerFailed(string $provider): self
    {
        return new self(
            "OAuth provider {$provider} failed",
            'error.oauth.provider_failed',
            ['provider' => $provider]
        );
    }

    public static function invalidState(): self
    {
        return new self(
            'Invalid OAuth state',
            'error.oauth.invalid_state'
        );
    }

    public static function tokenRetrievalFailed(): self
    {
        return new self(
            'Failed to retrieve OAuth token',
            'error.oauth.token_failed'
        );
    }
}
