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
            fileName: \App\Core\Domain\ValueObject\StreamFileName::create($entity->getFileName()),
            originalFileName: \App\Core\Domain\ValueObject\StreamOriginalFileName::create($entity->getOriginalFileName()),
            url: \App\Core\Domain\ValueObject\StreamUrl::create($entity->getUrl()),
            id: \App\Core\Domain\ValueObject\StreamId::create($entity->getId()->toString()),
        );
    }

    public function toEntity(StreamModel $model): Stream
    {
        return new Stream(
            fileName: $model->fileName?->value(),
            originalFileName: $model->originalFileName?->value(),
            url: $model->url?->value(),
        );
    }

    public function fromArray(array $data): StreamModel
    {
        return new StreamModel(
            fileName: \App\Core\Domain\ValueObject\StreamFileName::create($data['fileName'] ?? null),
            originalFileName: \App\Core\Domain\ValueObject\StreamOriginalFileName::create($data['originalFileName'] ?? null),
            url: \App\Core\Domain\ValueObject\StreamUrl::create($data['url'] ?? null),
            id: \App\Core\Domain\ValueObject\StreamId::create($data['id'] ?? null),
        );
    }

    public function toArray(StreamModel $model): array
    {
        return [
                'fileName' => $model->fileName?->valueView(),
                        'originalFileName' => $model->originalFileName?->valueView(),
                        'url' => $model->url?->valueView(),
                        'id' => $model->id?->valueView(),
                ];
    }
}
