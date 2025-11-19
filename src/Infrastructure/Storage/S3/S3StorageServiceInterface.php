<?php

declare(strict_types=1);

namespace App\Infrastructure\Storage\S3;

use App\Application\Upload\Dto\UploadedFileDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface S3StorageServiceInterface
{
    public function download(Uuid $uuid, string $fileName): string;

    public function upload(Uuid $uuid, UploadedFile $file): UploadedFileDto;

    public function deleteAll(Uuid $uuid): void;

    public function delete(Uuid $uuid, string $fileName): void;
}
