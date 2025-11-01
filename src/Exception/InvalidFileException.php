<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class InvalidFileException extends BusinessException
{
    public function __construct(
        string $englishMessage = 'Invalid file',
        string $translationKey = 'error.file.invalid',
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public static function invalidMimeType(string $mimeType, array $allowedTypes): self
    {
        return new self(
            sprintf('Invalid file type: %s. Allowed types: %s', $mimeType, implode(', ', $allowedTypes)),
            'error.file.invalid_mime_type',
            [
                'mimeType' => $mimeType,
                'allowedTypes' => implode(', ', $allowedTypes),
            ]
        );
    }

    public static function fileTooLarge(int $size, int $maxSize): self
    {
        return new self(
            sprintf('File too large: %d bytes. Maximum allowed: %d bytes', $size, $maxSize),
            'error.file.too_large',
            [
                'size' => $size,
                'maxSize' => $maxSize,
            ]
        );
    }

    public static function uploadFailed(string $reason = ''): self
    {
        return new self(
            'File upload failed'.($reason ? ": {$reason}" : ''),
            'error.file.upload_failed',
            ['reason' => $reason]
        );
    }
}
