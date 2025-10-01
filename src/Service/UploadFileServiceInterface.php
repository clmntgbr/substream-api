<?php

namespace App\Service;

use App\Core\Domain\Aggregate\UploadFileModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface UploadFileServiceInterface
{
    public function uploadVideo(Uuid $streamId, UploadedFile $file): UploadFileModel;
}
