<?php

namespace App\Application\CommandHandler;

use App\Application\Command\TransformVideoCommand;
use App\Application\Command\TransformVideoSuccessCommand;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TransformVideoSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(TransformVideoSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            return;
        }

        $stream->markAsTransformedVideo($command->videoFileTransformed);
        $this->streamRepository->save($stream);

        // $this->messageBus->dispatch(new TransformVideoCommand(
        //     streamId: $stream->getId(),
        // ));
    }
}
