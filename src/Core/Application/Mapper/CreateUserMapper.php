<?php

namespace App\Core\Application\Mapper;

use App\Core\Domain\Aggregate\CreateUserModel;
use App\Entity\User;

class CreateUserMapper implements CreateUserMapperInterface
{
    public function fromEntity(User $entity): CreateUserModel
    {
        return new CreateUserModel(
            userId: $entity->getId(),
        );
    }
}
