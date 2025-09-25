<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\UploadVideo;

use App\Core\Domain\Aggregate\UploadVideoModel;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\UploadFileName;
use App\Core\Domain\ValueObject\UploadOriginalFileName;

class UploadVideoMapper implements UploadVideoMapperInterface
{
    public function __construct(
    ) {
    }

    public function create(string $fileName, string $originalFileName, string $id): UploadVideoModel
    {
        return new UploadVideoModel(
            fileName: UploadFileName::create($fileName),
            originalFileName: UploadOriginalFileName::create($originalFileName),
            id: StreamId::create($id),
        );
    }
}
