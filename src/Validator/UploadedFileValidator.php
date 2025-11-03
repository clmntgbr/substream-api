<?php

declare(strict_types=1);

namespace App\Validator;

use App\Exception\InvalidFileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadedFileValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws InvalidFileException
     */
    public function validateVideo(UploadedFile $file): void
    {
        $this->validateFile(
            $file,
            ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'],
            524288000, // 500MB in bytes
            'video'
        );
    }

    /**
     * @throws InvalidFileException
     */
    public function validateThumbnail(UploadedFile $file): void
    {
        $this->validateFile(
            $file,
            ['image/jpeg', 'image/png', 'image/jpg'],
            10485760, // 10MB in bytes
            'thumbnail'
        );
    }

    /**
     * @param array<string> $allowedMimeTypes
     *
     * @throws InvalidFileException
     */
    private function validateFile(
        UploadedFile $file,
        array $allowedMimeTypes,
        int $maxSize,
        string $fileType,
    ): void {
        if (!$file->isValid()) {
            throw InvalidFileException::uploadFailed($file->getErrorMessage());
        }

        $mimeType = $file->getMimeType();
        if (null === $mimeType) {
            throw InvalidFileException::invalidMimeType('unknown', $allowedMimeTypes);
        }

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            throw InvalidFileException::invalidMimeType($mimeType, $allowedMimeTypes);
        }

        if ($file->getSize() > $maxSize) {
            throw InvalidFileException::fileTooLarge($file->getSize(), $maxSize);
        }

        $constraints = new File([
            'maxSize' => $maxSize,
            'mimeTypes' => $allowedMimeTypes,
            'mimeTypesMessage' => "error.validation.{$fileType}.invalid_format",
            'maxSizeMessage' => "error.validation.{$fileType}.too_large",
        ]);

        $violations = $this->validator->validate($file, $constraints);

        if (count($violations) > 0) {
            $firstViolation = $violations[0];
            if (null === $firstViolation) {
                throw new InvalidFileException('Validation failed', 'error.file.invalid', ['file' => $file->getClientOriginalName()]);
            }

            $message = $firstViolation->getMessage();
            throw new InvalidFileException(is_string($message) ? $message : (string) $message, 'error.file.invalid', ['file' => $file->getClientOriginalName()]);
        }
    }
}
