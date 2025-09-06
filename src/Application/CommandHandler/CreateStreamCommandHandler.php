<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateStreamCommand;
use App\Application\Command\GetVideoSuccessCommand;
use App\Application\Command\UploadVideoCommand;
use App\Entity\Stream;
use App\Exception\UserNotFoundException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use App\Service\MessageBusInterface;
use App\Service\UploadVideoServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateStreamCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private UserRepository $userRepository,
        private MessageBusInterface $messageBus,
        private UploadVideoServiceInterface $uploadVideoService,
    ) {
    }

    public function __invoke(CreateStreamCommand $command): void
    {
        $user = $this->userRepository->findOneBy(['id' => $command->userId]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $stream = (new Stream())->create(
            uuid: $command->uuid,
            user: $user,
        );

        $this->streamRepository->save($stream, true);

        $this->messageBus->dispatchs([
            new UploadVideoCommand(
                userId: $command->userId,
                streamId: $stream->getId(),
                file: $command->file,
            ),
            new GetVideoSuccessCommand(
                streamId: $stream->getId(),
            ),
        ]);
    }
}
