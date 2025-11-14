<?php

declare(strict_types=1);

namespace App\Infrastructure\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueEmail extends Constraint
{
    public string $message = 'This email address is already registered';

    public function validatedBy(): string
    {
        return static::class.'Validator';
    }
}
