<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\UpdateSearchNotificationsCommand;
use App\Core\Application\Message\ChunkVideoMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use App\Service\PublishServiceInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class UpdateSearchNotificationsCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private PublishServiceInterface $publishService,
        private UserRepository $userRepository,   
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(UpdateSearchNotificationsCommand $command): void
    {
        $user = $this->userRepository->findByUuid($command->getUserId());

        if (null === $user) {
            $this->logger->error('User not found', [
                'user_id' => $command->getUserId(),
            ]);

            return;
        }

        $this->publishService->refreshSearchNotifications($user, $command->getContext());
    }
}
