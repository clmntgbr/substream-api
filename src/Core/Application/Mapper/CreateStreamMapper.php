<?php

namespace App\Core\Application\Mapper;

use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;

class CreateStreamMapper implements CreateStreamMapperInterface
{
    public function fromEntity(Stream $entity): CreateStreamModel
    {
        return new CreateStreamModel(
            streamId: $entity->getId(),
        );
    }

    public function toEntity(CreateStreamModel $model): Stream
    {
        return new Stream(
            id: $model->streamId,
        );
    }
}
