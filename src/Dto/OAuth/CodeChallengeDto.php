<?php

declare(strict_types=1);

namespace App\Dto\OAuth;

class CodeChallengeDto
{
    public function __construct(
        private string $codeVerifier,
        private string $codeChallenge,
        private string $codeChallengeMethod,
    ) {
    }

    public function getCodeVerifier(): string
    {
        return $this->codeVerifier;
    }

    public function getCodeChallenge(): string
    {
        return $this->codeChallenge;
    }

    public function getCodeChallengeMethod(): string
    {
        return $this->codeChallengeMethod;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            codeVerifier: $data['code_verifier'],
            codeChallenge: $data['code_challenge'],
            codeChallengeMethod: $data['code_challenge_method'],
        );
    }

    public function toArray(): array
    {
        return [
            'code_verifier' => $this->codeVerifier,
            'code_challenge' => $this->codeChallenge,
            'code_challenge_method' => $this->codeChallengeMethod,
        ];
    }
}
