<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler\Async;

use App\Client\Processor\ExtractSoundProcessorInterface;
use App\Core\Application\Command\Async\ExtractSoundCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\ExtractSound;
use App\Exception\ProcessorException;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;
use Psr\Log\LoggerInterface;

#[AsMessageHandler]
class ExtractSoundCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
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

        // try {
        //     $this->streamsStateMachine->apply($stream, 'extract_sound');
        //     ($this->processor)(new ExtractSound(
        //         stream: $stream,
        //         fileName: $command->getFileName(),
        //     ));
        // } catch (ProcessorException $exception) {
        //     $stream->markAsExtractSoundFailed();
        // } finally {
        //     $this->streamRepository->save($stream);
        // }
    }
}
