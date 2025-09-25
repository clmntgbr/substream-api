<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\UploadVideo;

use App\Core\Domain\Aggregate\UploadVideoModel;
use Symfony\Component\Uid\Uuid;

interface UploadVideoMapperInterface
{
    public function create(string $fileName, Uuid $id): UploadVideoModel;
}
