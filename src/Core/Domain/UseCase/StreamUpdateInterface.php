<?php

declare(strict_types=1);

namespace App\Core\Domain\UseCase;

/**
 * Interface StreamUpdateInterface* Defines the contract for updating Stream entities.
 */
interface StreamUpdateInterface
{
    public function update(\App\Core\Domain\Aggregate\StreamModel $entity, \App\Core\Domain\ValueObject\StreamId $entityId): \App\Core\Domain\Aggregate\StreamModel;
}
