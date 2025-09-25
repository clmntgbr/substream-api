<?php

declare(strict_types=1);

namespace App\Core\Domain\UseCase;

/**
 * Interface StreamCreateInterface* Defines the contract for creating a Stream.
 */
interface StreamCreateInterface
{
    public function create(\App\Core\Domain\Aggregate\StreamModel $stream): \App\Core\Domain\Aggregate\StreamModel;
}
