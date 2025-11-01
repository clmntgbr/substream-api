<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class BusinessException extends \Exception
{
    public function __construct(
        private readonly string $englishMessage,
        private readonly string $translationKey,
        private readonly array $translationParams = [],
        int $httpStatusCode = Response::HTTP_BAD_REQUEST,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($englishMessage, $httpStatusCode, $previous);
    }

    public function getEnglishMessage(): string
    {
        return $this->englishMessage;
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function getTranslationParams(): array
    {
        return $this->translationParams;
    }

    public function getHttpStatusCode(): int
    {
        return $this->getCode();
    }
}
