<?php

namespace App\Core\Application\Mapper;

use App\Core\Domain\Aggregate\CreateUserModel;
use App\Entity\User;

interface CreateUserMapperInterface
{
    public function fromEntity(User $entity): CreateUserModel;
}
