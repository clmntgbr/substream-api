<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\EmbedVideoCommand;
use App\Core\Application\Message\EmbedVideoMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class EmbedVideoCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
    ) {
    }

    public function __invoke(EmbedVideoCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->apply($stream, WorkflowTransitionEnum::EMBEDDING_VIDEO);
        $this->streamRepository->save($stream);

        $this->coreBus->dispatch(new EmbedVideoMessage(
            streamId: $stream->getId(),
            subtitleAssFileName: $command->getSubtitleAssFileName(),
            resizedFileName: $command->getResizedFileName(),
        ));
    }
}
