<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper;

use App\Core\Domain\Aggregate\UploadFileModel;
use Symfony\Component\Uid\Uuid;

interface UploadFileMapperInterface
{
    public function create(
        string $fileName,
        string $originalFileName,
        Uuid $id,
    ): UploadFileModel;
}
