<?php

namespace App\Fixtures\Provider;

use App\Entity\ValueObject\Email;
use Symfony\Component\Uid\Uuid;

class UuidProvider
{
    public function uuid(string $uuid): Uuid
    {
        return Uuid::fromString($uuid);
    }
}