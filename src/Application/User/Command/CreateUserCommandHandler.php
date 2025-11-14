<?php

declare(strict_types=1);

namespace App\Application\User\Command;

use App\Application\Subscription\Command\CreateSubscriptionCommand;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateUserCommandHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(CreateUserCommand $command): User
    {
        $user = User::create(
            firstname: $command->getFirstname(),
            lastname: $command->getLastname(),
            picture: $command->getPicture(),
            email: $command->getEmail(),
            plainPassword: $command->getPlainPassword(),
        );

        $subscription = $this->commandBus->dispatch(new CreateSubscriptionCommand(
            user: $user,
            planReference: 'plan_free',
        ));

        $user->addSubscription($subscription);
        $this->userRepository->saveAndFlush($user);

        return $user;
    }
}
