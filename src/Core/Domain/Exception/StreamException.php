<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

/** Exception for Stream domain errors */
class StreamException extends \Exception
{
    public function __construct(string $message = 'An error occurred in Stream domain')
    {
        parent::__construct($message);
    }

    public static function because(string $reason): self
    {
        return new self($reason);
    }
}
