<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateOrUpdateUserCommand;
use App\Core\Application\Command\CreateUserCommand;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateOrUpdateUserCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateOrUpdateUserCommand $command): User
    {
        $user = $this->userRepository->findOneBy(['email' => $command->getEmail()]);

        if (null === $user) {
            $user = $this->commandBus->dispatch(new CreateUserCommand(
                firstname: $command->getFirstname(),
                lastname: $command->getLastname(),
                email: $command->getEmail(),
                plainPassword: bin2hex(random_bytes(16)),
            ));
        }

        return $user;
    }
}
