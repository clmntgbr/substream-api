<?php

declare(strict_types=1);

namespace App\Core\Domain\Repository;

use App\Core\Domain\Aggregate\StreamModel;
use App\Core\Domain\ValueObject\StreamId;

interface StreamRepositoryInterface
{
    public function save(StreamModel $stream): StreamModel;

    public function update(StreamModel $stream, StreamId $id): StreamModel;

    public function delete(StreamId $stream): StreamId;

    public function findById(StreamId $id): ?StreamModel;

    /**
     * @return array<\App\Core\Domain\Aggregate\StreamModel>
     */
    public function findAll(): array;

    /**
     * @return array<\App\Core\Domain\Aggregate\StreamModel>
     */
    public function findByCriteria(array $criteria): array;

    /**
     * @return array{
     *     items: array<\App\Core\Domain\Aggregate\StreamModel>,
     *     total: int,
     *     page: int,
     *     limit: int
     * }
     */
    public function findPaginated(int $page, int $limit, array $criteria = []): array;
}
