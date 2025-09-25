<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\UploadVideo;

use App\Core\Domain\Aggregate\UploadVideoModel;

interface UploadVideoMapperInterface
{
    public function create(string $fileName, string $originalFileName, string $id): UploadVideoModel;
}
