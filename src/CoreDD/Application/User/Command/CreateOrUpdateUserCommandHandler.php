<?php

declare(strict_types=1);

namespace App\CoreDD\Application\User\Command;

use App\CoreDD\Domain\User\Entity\User;
use App\CoreDD\Domain\User\Repository\UserRepository;
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
                email: $command->getEmail(),
                plainPassword: bin2hex(random_bytes(16)),
                firstname: $command->getFirstname(),
                lastname: $command->getLastname(),
                picture: $command->getPicture(),
            ));
        }

        return $user;
    }
}
