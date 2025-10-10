<?php

namespace App\Service;

use App\Core\Domain\Aggregate\UploadFileModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface S3ServiceInterface
{
    public function download(Uuid $uuid, string $fileName): string;
    
    public function upload(Uuid $uuid, UploadedFile $file): UploadFileModel;

    public function deleteAll(Uuid $uuid): void;

    public function delete(Uuid $uuid, string $fileName): void;
}
