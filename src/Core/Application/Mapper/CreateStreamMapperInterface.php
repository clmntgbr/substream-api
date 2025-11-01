<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper;

use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;

interface CreateStreamMapperInterface
{
    public function fromEntity(Stream $entity): CreateStreamModel;

    public function toEntity(CreateStreamModel $model): Stream;
}
