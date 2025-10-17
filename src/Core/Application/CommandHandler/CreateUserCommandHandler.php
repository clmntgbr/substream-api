<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateUserCommand;
use App\Core\Application\Mapper\CreateUserMapperInterface;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateUserModel;
use App\Entity\User;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private UserRepository $userRepository,
        private LoggerInterface $logger,
        private CreateUserMapperInterface $createUserMapper,
    ) {
    }

    public function __invoke(CreateUserCommand $command): CreateUserModel
    {
        $user = User::create(
            firstname: $command->getFirstname(),
            lastname: $command->getLastname(),
            email: $command->getEmail(),
            plainPassword: $command->getPlainPassword(),
        );
        $this->userRepository->save($user, true);

        return $this->createUserMapper->fromEntity($user);
    }
}
