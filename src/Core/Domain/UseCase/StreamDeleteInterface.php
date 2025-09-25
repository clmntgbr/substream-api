<?php

declare(strict_types=1);

namespace App\Core\Domain\UseCase;

/**
 * Interface StreamDeleteInterface* Defines the contract for deleting a Stream.
 */
interface StreamDeleteInterface
{
    public function delete(\App\Core\Domain\ValueObject\StreamId $id): \App\Core\Domain\ValueObject\StreamId;
}
