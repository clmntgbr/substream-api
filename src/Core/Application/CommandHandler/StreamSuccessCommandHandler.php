<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CleanStreamCommand;
use App\Core\Application\Command\CreateStreamSuccessNotificationCommand;
use App\Core\Application\Command\StreamSuccessCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class StreamSuccessCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CommandBusInterface $commandBus,
        private PublishServiceInterface $publishService,
    ) {
    }

    public function __invoke(StreamSuccessCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => StreamSuccessCommand::class,
            ]);

            return;
        }

        $this->complete($stream);

        $this->commandBus->dispatch(new CleanStreamCommand(
            streamId: $stream->getId(),
        ));

        $this->commandBus->dispatch(new CreateStreamSuccessNotificationCommand(
            streamId: $stream->getId(),
        ));

        $this->publishService->refreshStream($stream, StreamSuccessCommand::class);
        $this->publishService->refreshSearchStreams($stream, StreamSuccessCommand::class);
    }

    private function complete(Stream $stream): void
    {
        if ($stream->getStatus() === StreamStatusEnum::RESUMING_FAILED->value) {
            $this->apply($stream, WorkflowTransitionEnum::COMPLETED_RESUME_FAILED);
            $this->streamRepository->saveAndFlush($stream);

            return;
        }

        if (!$stream->getOption()->getIsResume()) {
            $this->apply($stream, WorkflowTransitionEnum::COMPLETED_NO_RESUME);
            $this->streamRepository->saveAndFlush($stream);

            return;
        }

        if ($stream->getOption()->getIsResume()) {
            $this->apply($stream, WorkflowTransitionEnum::COMPLETED);
            $this->streamRepository->saveAndFlush($stream);
        }
    }
}
