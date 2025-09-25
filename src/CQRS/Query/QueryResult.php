<?php

declare(strict_types=1);

namespace App\CQRS\Query;

class QueryResult
{
    public function __construct(
        private mixed $data = null,
        private ?string $error = null
    ) {
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public function isFailure(): bool
    {
        return $this->error !== null;
    }
}
