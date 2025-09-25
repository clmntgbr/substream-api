<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Domain\Aggregate\UploadVideoModel;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface UploadServiceInterface
{
    public function uploadVideo(UploadedFile $file): UploadVideoModel;
}