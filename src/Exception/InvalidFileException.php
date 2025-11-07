<?php

declare(strict_types=1);

namespace App\Exception;

use App\Enum\ErrorKeyEnum;
use Symfony\Component\HttpFoundation\Response;

class InvalidFileException extends BusinessException
{
    /**
     * @param array<string, mixed> $translationParams
     */
    public function __construct(
        string $englishMessage = 'Invalid file',
        string $translationKey = ErrorKeyEnum::FILE_INVALID->value,
        array $translationParams = [],
    ) {
        parent::__construct($englishMessage, $translationKey, $translationParams, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @param array<string> $allowedTypes
     */
    public static function invalidMimeType(string $mimeType, array $allowedTypes): self
    {
        return new self(
            sprintf('Invalid file type: %s. Allowed types: %s', $mimeType, implode(', ', $allowedTypes)),
            ErrorKeyEnum::FILE_INVALID_MIME_TYPE->value,
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
            ErrorKeyEnum::FILE_TOO_LARGE->value,
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
            ErrorKeyEnum::FILE_UPLOAD_FAILED->value,
            ['reason' => $reason]
        );
    }
}
