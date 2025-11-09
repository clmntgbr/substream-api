<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\UserRepository;
use App\Service\PublishServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'dummy:command',
    description: 'Dummy command',
)]
class DummyCommand extends Command
{
    public function __construct(
        private readonly PublishServiceInterface $publishService,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $user = $this->userRepository->findOneBy(['email' => 'dummy@gmail.com']);

        $this->publishService->refreshPlan($user);

        return Command::SUCCESS;
    }
}
