<?php

namespace App\Application\CommandHandler;

use App\Application\Command\TransformSubtitlesCommand;
use App\Application\Command\TransformSubtitlesSuccessCommand;
use App\Repository\StreamRepository;
use App\Service\MessageBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TransformSubtitlesSuccessCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(TransformSubtitlesSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            return;
        }

        $stream->markAsTransformedSubtitles($command->subtitleAssFile);
        $this->streamRepository->save($stream);

        // $this->messageBus->dispatch(new TransformSubtitlesCommand(
        //     streamId: $stream->getId(),
        // ));
    }
}
