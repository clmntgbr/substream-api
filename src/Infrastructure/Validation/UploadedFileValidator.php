<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation;

use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function count;
use function in_array;
use function is_string;

class UploadedFileValidator
{
    public function __construct(
        private readonly ValidatorInterface $validator,
    ) {
    }

    /**
     * @throws Exception
     */
    public function validateVideo(UploadedFile $file): void
    {
        $this->validateFile(
            $file,
            ['video/mp4', 'video/mpeg', 'video/quicktime', 'video/x-msvideo'],
            524288000,
            'video'
        );
    }

    /**
     * @throws Exception
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
     * @throws Exception
     */
    private function validateFile(
        UploadedFile $file,
        array $allowedMimeTypes,
        int $maxSize,
        string $fileType,
    ): void {
        if (! $file->isValid()) {
            throw new Exception($file->getErrorMessage());
        }

        $mimeType = $file->getMimeType();
        if (null === $mimeType) {
            throw new Exception('Invalid mime type');
        }

        if (! in_array($mimeType, $allowedMimeTypes, true)) {
            throw new Exception('Invalid mime type');
        }

        if ($file->getSize() > $maxSize) {
            throw new Exception('File too large');
        }

        $constraints = new File([
            'maxSize' => $maxSize,
            'mimeTypes' => $allowedMimeTypes,
            'mimeTypesMessage' => 'Invalid mime type',
            'maxSizeMessage' => 'File too large',
        ]);

        $violations = $this->validator->validate($file, $constraints);

        if (count($violations) > 0) {
            $firstViolation = $violations[0];
            if (null === $firstViolation) {
                throw new Exception('Validation failed');
            }

            $message = $firstViolation->getMessage();
            throw new Exception(is_string($message) ? $message : (string) $message);
        }
    }
}
