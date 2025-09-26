<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\UploadVideo;

use App\Core\Domain\Aggregate\UploadVideoModel;
use App\Core\Domain\ValueObject\FileName;
use App\Core\Domain\ValueObject\OriginalFileName;
use App\Core\Domain\ValueObject\StreamId;

class UploadVideoMapper implements UploadVideoMapperInterface
{
    public function __construct(
    ) {
    }

    public function create(string $fileName, string $originalFileName, string $id): UploadVideoModel
    {
        return new UploadVideoModel(
            fileName: FileName::create($fileName),
            originalFileName: OriginalFileName::create($originalFileName),
            id: StreamId::create($id),
        );
    }
}
