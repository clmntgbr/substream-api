<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateSocialAccountCommand;
use App\Core\Domain\SocialAccount\Entity\SocialAccount;
use App\Core\Domain\SocialAccount\Repository\SocialAccountRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateSocialAccountCommandHandler
{
    public function __construct(
        private SocialAccountRepository $socialAccountRepository,
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

        $this->socialAccountRepository->saveAndFlush($socialAccount);

        return $socialAccount;
    }
}
