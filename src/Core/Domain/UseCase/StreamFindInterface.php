<?php

declare(strict_types=1);

namespace App\Core\Domain\UseCase;

/**
 * Interface StreamFindInterface* Defines the contract for querying Stream entities.
 */
interface StreamFindInterface
{
    public function find(\App\Core\Domain\ValueObject\StreamId $id): ?\App\Core\Domain\Aggregate\StreamModel;

    public function findAll(): array;

    public function findPaginated(int $page, int $limit, array $criteria = []): array;
}
