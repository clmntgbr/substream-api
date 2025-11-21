<?php

declare(strict_types=1);

namespace App\Infrastructure\Cli;

use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'dummy:command',
    description: 'Dummy command',
)]
class DummyCommand extends Command
{
    public function __construct(
        private MercurePublisherInterface $publisher,
        private UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userRepository->findByUuid(Uuid::fromString('019aa69b-8f23-7680-844d-6876849b070d'));

        $this->publisher->refreshPlan($user);
        $this->publisher->refreshSubscription($user);

        return Command::SUCCESS;
    }
}
