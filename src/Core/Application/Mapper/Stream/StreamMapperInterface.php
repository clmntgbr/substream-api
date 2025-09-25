<?php

declare(strict_types=1);

namespace App\Core\Application\Mapper\Stream;

use App\Core\Domain\Aggregate\StreamModel;
use App\Entity\Stream;

interface StreamMapperInterface
{
    public function fromEntity(Stream $entity): StreamModel;

    public function toEntity(StreamModel $model): Stream;

    public function fromArray(array $data): StreamModel;

    public function toArray(StreamModel $model): array;
}
