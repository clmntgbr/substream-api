<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\Stream;

use App\Core\Domain\Aggregate\StreamModel;
use App\Core\Domain\ValueObject\StreamFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\StreamOriginalFileName;
use App\Core\Domain\ValueObject\StreamUrl;
use App\Entity\Stream;

class StreamMapper implements StreamMapperInterface
{
    public function __construct(
    ) {
    }

    public function fromEntity(Stream $entity): StreamModel
    {
        return new StreamModel(
            fileName: StreamFileName::create($entity->getFileName()),
            originalFileName: StreamOriginalFileName::create($entity->getOriginalFileName()),
            url: StreamUrl::create($entity->getUrl()),
            id: StreamId::create($entity->getId()->toString()),
        );
    }

    public function toEntity(StreamModel $model): Stream
    {
        return Stream::create(
            fileName: $model->fileName?->value(),
            originalFileName: $model->originalFileName?->value(),
            url: $model->url?->value(),
        );
    }

    public function fromArray(array $data): StreamModel
    {
        return new StreamModel(
            fileName: StreamFileName::create($data['fileName'] ?? null),
            originalFileName: StreamOriginalFileName::create($data['originalFileName'] ?? null),
            url: StreamUrl::create($data['url'] ?? null),
            id: StreamId::create($data['id'] ?? null),
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
