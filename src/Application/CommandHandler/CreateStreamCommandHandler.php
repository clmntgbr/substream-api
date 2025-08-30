<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateStreamCommand;
use App\Entity\Stream;
use App\Exception\UserNotFoundException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): void
    {
        $user = $this->userRepository->find($command->userId);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $stream = (new Stream())->create(
            uuid: $command->uuid,
            fileName: $command->fileName,
            originalName: $command->originalName,
            mimeType: $command->mimeType,
            size: $command->size,
            user: $user,
        );

        $this->streamRepository->save($stream, true);
    }
}
