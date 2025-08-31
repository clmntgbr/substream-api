<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface UploadVideoServiceInterface
{
    public function upload(UploadedFile $file, Uuid $userId, Uuid $streamId): void;

    public function uploadByUrl(string $url, Uuid $userId, Uuid $streamId): void;
}
