<?php

namespace App\Service;

use App\Core\Domain\Aggregate\UploadFileModel;
use App\Entity\Stream;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

interface BuildArchiveServiceInterface
{
    public function build(Stream $stream): File;
}
