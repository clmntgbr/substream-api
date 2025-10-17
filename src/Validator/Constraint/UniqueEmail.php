<?php

namespace App\Validator\Constraint;

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
