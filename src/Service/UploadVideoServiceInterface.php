<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploadVideoServiceInterface
{
    public function upload(UploadedFile $file): void;

    public function uploadByUrl(string $url): void;
}
