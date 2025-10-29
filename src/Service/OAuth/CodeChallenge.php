<?php

namespace App\Service\OAuth;

use App\Dto\OAuth\CodeChallengeDto;

class CodeChallenge
{
    public static function generate(): CodeChallengeDto
    {
        $codeVerifier = self::generateVerifier();
        $codeChallengeMethod = self::generateMethod();

        // Generate code challenge using S256 method (RFC 7636)
        $codeChallenge = base64_encode(hash('sha256', $codeVerifier, true));
        $codeChallenge = strtr($codeChallenge, '+/', '-_');
        $codeChallenge = rtrim($codeChallenge, '=');

        return new CodeChallengeDto(
            codeVerifier: $codeVerifier,
            codeChallenge: $codeChallenge,
            codeChallengeMethod: $codeChallengeMethod,
        );
    }

    private static function generateMethod(): string
    {
        return 'S256';
    }

    private static function generateVerifier(): string
    {
        return bin2hex(random_bytes(32));
    }
}
