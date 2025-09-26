<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\Stream;

use App\Core\Domain\Aggregate\StreamModel;
use App\Core\Domain\ValueObject\FileName;
use App\Core\Domain\ValueObject\OriginalFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\Url;
use App\Entity\Stream;

class StreamMapper implements StreamMapperInterface
{
    public function __construct(
    ) {
    }

    public function fromEntity(Stream $entity): StreamModel
    {
        return new StreamModel(
            fileName: FileName::create($entity->getFileName()),
            originalFileName: OriginalFileName::create($entity->getOriginalFileName()),
            url: Url::create($entity->getUrl()),
            id: StreamId::create($entity->getId()->toString()),
        );
    }

    public function toEntity(StreamModel $model): Stream
    {
        return Stream::create(
            id: $model->id?->value(),
            fileName: $model->fileName?->value(),
            originalFileName: $model->originalFileName?->value(),
            url: $model->url?->value(),
        );
    }

    public function fromArray(array $data): StreamModel
    {
        return new StreamModel(
            fileName: FileName::create($data['fileName'] ?? null),
            originalFileName: OriginalFileName::create($data['originalFileName'] ?? null),
            url: Url::create($data['url'] ?? null),
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
