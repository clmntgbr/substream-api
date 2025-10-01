<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Client\Processor\ExtractSoundProcessorInterface;
use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Message\ExtractSoundMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\ExtractSound;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;
use Psr\Log\LoggerInterface;
use App\Shared\Application\Bus\CoreBusInterface;

#[AsMessageHandler]
class ExtractSoundCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
    ) {
    }

    public function __invoke(ExtractSoundCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => $command->getStreamId(),
            ]);

            return;
        }

        $this->coreBus->dispatch(new ExtractSoundMessage(
            streamId: $stream->getId(),
            fileName: $command->getFileName(),
        ));
    }
}
