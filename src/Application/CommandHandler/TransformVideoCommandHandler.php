<?php

namespace App\Application\CommandHandler;

use App\Application\Command\TransformVideoCommand;
use App\Exception\StreamNotFoundException;
use App\Repository\StreamRepository;
use App\Service\TransformVideoServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TransformVideoCommandHandler
{
    public function __construct(
        private StreamRepository $streamRepository,
        private TransformVideoServiceInterface $transformVideoService,
    ) {
    }

    public function __invoke(TransformVideoCommand $command): void
    {
        $stream = $this->streamRepository->findOneBy(['id' => $command->streamId]);
        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        $stream->markAsTransformingVideoProcessing();
        $this->transformVideoService->transformVideo($stream);
    }
}
