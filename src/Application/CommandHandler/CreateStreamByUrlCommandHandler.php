<?php

namespace App\Application\CommandHandler;

use App\Application\Command\CreateStreamByUrlCommand;
use App\Application\Command\UploadVideoByUrlCommand;
use App\Entity\Stream;
use App\Exception\UserNotFoundException;
use App\Repository\StreamRepository;
use App\Repository\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpStamp;
use Symfony\Component\Messenger\MessageBusInterface;

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
        $user = $this->userRepository->find($command->userId);
        if (null === $user) {
            throw new UserNotFoundException();
        }

        $stream = (new Stream())->create(
            uuid: $command->uuid,
            user: $user,
        );

        $this->streamRepository->save($stream, true);

        $this->messageBus->dispatch(new UploadVideoByUrlCommand(
            userId: $command->userId,
            streamId: $stream->getId(),
            url: $command->url,
        ), [new AmqpStamp('async-high')]);
    }
}
