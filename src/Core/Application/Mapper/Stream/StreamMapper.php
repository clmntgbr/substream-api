<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\Stream;

use App\Core\Domain\Aggregate\StreamModel;
use App\Entity\Stream;

class StreamMapper implements StreamMapperInterface
{
    public function __construct(
    ) {
    }

    public function fromEntity(Stream $entity): StreamModel
    {
        return new StreamModel(
            id: \App\Core\Domain\ValueObject\StreamId::create($entity->getId()->toString()),
        );
    }

    public function toEntity(StreamModel $model): Stream
    {
        return new Stream(
        );
    }

    public function fromArray(array $data): StreamModel
    {
        return new StreamModel(
            id: \App\Core\Domain\ValueObject\StreamId::create($data['id'] ?? null),
        );
    }

    public function toArray(StreamModel $model): array
    {
        return [
                'id' => $model->id?->valueView(),
                ];
    }
}
