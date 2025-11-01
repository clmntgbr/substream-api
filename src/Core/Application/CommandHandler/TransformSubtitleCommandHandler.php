<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\TransformSubtitleCommand;
use App\Core\Application\Message\TransformSubtitleMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\PublishServiceInterface;
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
        private TaskRepository $taskRepository,
        private PublishServiceInterface $publishService,
    ) {
    }

    public function __invoke(TransformSubtitleCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => TransformSubtitleCommand::class,
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::TRANSFORMING_SUBTITLE);
            $this->streamRepository->saveAndFlush($stream);

            $task = Task::create(TransformSubtitleCommand::class, $stream);
            $this->taskRepository->saveAndFlush($task, true);

            $this->coreBus->dispatch(new TransformSubtitleMessage(
                taskId: $task->getId(),
                streamId: $stream->getId(),
                option: $stream->getOption(),
                subtitleSrtFileName: $command->getSubtitleSrtFileName(),
            ));
        } catch (\Exception) {
            $stream->markAsTransformingSubtitleFailed();
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->publishService->refreshStream($stream, TransformSubtitleCommand::class);
            $this->publishService->refreshSearchStreams($stream, TransformSubtitleCommand::class);
        }
    }
}
