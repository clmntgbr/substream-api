<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\UploadVideo;

use App\Core\Domain\Aggregate\UploadVideoModel;
use App\Core\Domain\ValueObject\UploadVideoFileName;
use Symfony\Component\Uid\Uuid;

class UploadVideoMapper implements UploadVideoMapperInterface
{
    public function __construct(
    ) {
    }

    public function create(string $fileName, Uuid $id): UploadVideoModel
    {
        return new UploadVideoModel(
            fileName: UploadVideoFileName::create($fileName),
            id: $id,
        );
    }
}