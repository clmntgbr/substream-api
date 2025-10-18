<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateSocialAccountCommand;
use App\Core\Application\Command\CreateUserCommand;
use App\Core\Application\Mapper\CreateUserMapperInterface;
use App\Core\Application\Trait\WorkflowTrait;
use App\Core\Domain\Aggregate\CreateUserModel;
use App\Entity\SocialAccount;
use App\Entity\User;
use App\Repository\SocialAccountRepository;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateSocialAccountCommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private SocialAccountRepository $socialAccountRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(CreateSocialAccountCommand $command): SocialAccount
    {
        $socialAccount = SocialAccount::create(
            provider: $command->getProvider(),
            accountId: $command->getAccountId(),
            email: $command->getEmail(),
            user: $command->getUser(),
        );

        $this->socialAccountRepository->save($socialAccount, true);
        return $socialAccount;
    }
}
