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
     * @throws InvalidFileException
     */
    private function validateFile(
        UploadedFile $file,
        array $allowedMimeTypes,
        int $maxSize,
        string $fileType,
    ): void {
        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            throw InvalidFileException::uploadFailed($file->getErrorMessage());
        }

        // Validate mime type
        if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
            throw InvalidFileException::invalidMimeType($file->getMimeType(), $allowedMimeTypes);
        }

        // Validate file size
        if ($file->getSize() > $maxSize) {
            throw InvalidFileException::fileTooLarge($file->getSize(), $maxSize);
        }

        // Use Symfony validator for additional checks
        $constraints = new File([
            'maxSize' => $maxSize,
            'mimeTypes' => $allowedMimeTypes,
            'mimeTypesMessage' => "error.validation.{$fileType}.invalid_format",
            'maxSizeMessage' => "error.validation.{$fileType}.too_large",
        ]);

        $violations = $this->validator->validate($file, $constraints);

        if (count($violations) > 0) {
            $firstViolation = $violations[0];
            throw new InvalidFileException($firstViolation->getMessage(), ['file' => $file->getClientOriginalName()]);
        }
    }
}
