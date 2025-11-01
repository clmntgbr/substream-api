<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GenerateSubtitleCommand;
use App\Core\Application\Command\TransformSubtitleCommand;
use App\Core\Application\Command\UpdateTaskSuccessCommand;
use App\Core\Application\Message\GenerateSubtitleMessage;
use App\Core\Application\Trait\WorkflowTrait;
use App\Entity\Stream;
use App\Entity\Task;
use App\Enum\WorkflowTransitionEnum;
use App\Repository\StreamRepository;
use App\Repository\TaskRepository;
use App\Service\PublishServiceInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use App\Shared\Application\Bus\CoreBusInterface;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\Exception\TransitionException as WorkflowTransitionException;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class GenerateSubtitleCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private WorkflowInterface $streamsStateMachine,
        private LoggerInterface $logger,
        private CoreBusInterface $coreBus,
        private TaskRepository $taskRepository,
        private FilesystemOperator $awsStorage,
        private CommandBusInterface $commandBus,
        private PublishServiceInterface $publishService,
        private string $env,
    ) {
    }

    public function __invoke(GenerateSubtitleCommand $command): void
    {
        $stream = $this->streamRepository->findByUuid($command->getStreamId());

        if (null === $stream) {
            $this->logger->error('Stream not found', [
                'stream_id' => (string) $command->getStreamId(),
                'command' => GenerateSubtitleCommand::class,
            ]);

            return;
        }

        try {
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE);
            $this->streamRepository->saveAndFlush($stream);

            $task = Task::create(GenerateSubtitleCommand::class, $stream);
            $this->taskRepository->saveAndFlush($task, true);

            if ('prod' === $this->env) {
                $this->coreBus->dispatch(new GenerateSubtitleMessage(
                    taskId: $task->getId(),
                    streamId: $stream->getId(),
                    audioFiles: $command->getAudioFiles(),
                    language: $command->getLanguage(),
                ));

                return;
            }

            $this->mockGenerateSubtitle($stream, $task);
        } catch (WorkflowTransitionException $e) {
            $this->logger->error('Workflow transition failed during subtitle generation', [
                'stream_id' => (string) $command->getStreamId(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $stream->markAsGeneratingSubtitleFailed();
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error during subtitle generation', [
                'stream_id' => (string) $command->getStreamId(),
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
                'trace' => $e->getTraceAsString(),
            ]);

            $stream->markAsGeneratingSubtitleFailed();
            $this->streamRepository->saveAndFlush($stream);
        } finally {
            $this->publishService->refreshStream($stream, GenerateSubtitleCommand::class);
            $this->publishService->refreshSearchStreams($stream, GenerateSubtitleCommand::class);
        }
    }

    private function mockGenerateSubtitle(Stream $stream, Task $task): void
    {
        $subtitleSrtFileName = $stream->getId().'.srt';

        $path = $stream->getId().'/subtitles/'.$subtitleSrtFileName;
        $handle = fopen('/app/public/debug/1bba6dc7-21ed-41c2-9694-6a2ea4db41fd.srt', 'r');

        $this->awsStorage->writeStream($path, $handle, [
            'visibility' => 'public',
        ]);

        if (is_resource($handle)) {
            fclose($handle);
        }

        try {
            $stream->setSubtitleSrtFileName($subtitleSrtFileName);
            $this->apply($stream, WorkflowTransitionEnum::GENERATING_SUBTITLE_COMPLETED);
            $this->streamRepository->saveAndFlush($stream);

            $this->commandBus->dispatch(new TransformSubtitleCommand(
                streamId: $stream->getId(),
                subtitleSrtFileName: $stream->getSubtitleSrtFileName(),
            ));
        } catch (WorkflowTransitionException $e) {
            $this->logger->error('Workflow transition failed in mock subtitle generation', [
                'stream_id' => (string) $stream->getId(),
                'error' => $e->getMessage(),
            ]);

            $stream->markAsGenerateSubtitleFailed();
            $this->streamRepository->saveAndFlush($stream);
        } catch (\Throwable $e) {
            $this->logger->error('Unexpected error in mock subtitle generation', [
                'stream_id' => (string) $stream->getId(),
                'error' => $e->getMessage(),
                'exception_class' => $e::class,
            ]);

            $stream->markAsGenerateSubtitleFailed();
            $this->streamRepository->saveAndFlush($stream);
        }

        $this->commandBus->dispatch(new UpdateTaskSuccessCommand(
            taskId: $task->getId(),
            processingTime: 0,
        ));
    }
}
