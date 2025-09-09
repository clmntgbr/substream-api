<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateStreamByUrlCommand;
use App\Application\Command\UploadVideoByUrlCommand;
use App\Entity\Stream;
use App\Exception\UserNotFoundException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateStreamByUrlCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private UserRepository $userRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(CreateStreamByUrlCommand $command): void
    {
        $user = $this->userRepository->findOneBy(['id' => $command->userId]);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $stream = (new Stream())->create(
            uuid: $command->uuid,
            user: $user,
            url: $command->url,
            options: $command->options,
        );

        $this->streamRepository->save($stream, true);

        $this->messageBus->dispatch(new UploadVideoByUrlCommand(
            userId: $command->userId,
            streamId: $stream->getId(),
            url: $command->url,
        ));
    }
}
