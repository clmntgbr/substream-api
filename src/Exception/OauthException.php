<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\TranslatableKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class OauthException extends BusinessException
{
    /**
     * @param array<string, mixed> $translationParams
     */
    public function __construct(
        string $englishMessage = 'OAuth authentication failed',
        string $translationKey = TranslatableKeyEnum::OAUTH_FAILED->value,
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_UNAUTHORIZED);
    }

    public static function providerFailed(string $provider): self
    {
        return new self(
            "OAuth provider {$provider} failed",
            TranslatableKeyEnum::OAUTH_PROVIDER_FAILED->value,
            ['provider' => $provider]
        );
    }

    public static function invalidState(): self
    {
        return new self(
            'Invalid OAuth state',
            TranslatableKeyEnum::OAUTH_INVALID_STATE->value
        );
    }

    public static function tokenRetrievalFailed(): self
    {
        return new self(
            'Failed to retrieve OAuth token',
            TranslatableKeyEnum::OAUTH_TOKEN_FAILED->value
        );
    }
}
