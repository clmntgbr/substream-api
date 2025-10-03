<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Core\Application\Message\TransformSubtitleMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CoreBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class TransformSubtitleCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
    ) {
    }

    public function __invoke(TransformSubtitleCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->apply($stream, WorkflowTransitionEnum::TRANSFORMING_SUBTITLE);
        $this->streamRepository->save($stream);

        $this->coreBus->dispatch(new TransformSubtitleMessage(
            streamId: $stream->getId(),
            subtitleSrtFileName: $command->getSubtitleSrtFileName(),
        ));
    }
}
