<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Client\Processor\ExtractSoundProcessorInterface;
use App\Core\Application\Command\ExtractSoundCommand;
use App\Core\Application\Trait\WorkflowTrait;
use App\Dto\ExtractSound;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsMessageHandler]
class ExtractSoundCommandHandler
{
    use WorkflowTrait;

    public function __construct(
        private StreamRepository $streamRepository,
        private ExtractSoundProcessorInterface $processor,
        private WorkflowInterface $streamsStateMachine,
        private JobRepository $jobRepository,
    ) {
    }

    public function __invoke(ExtractSoundCommand $command): void
    {
        $stream = $this->streamRepository->find($command->streamId);

        if (null === $stream) {
            throw new StreamNotFoundException();
        }

        try {
            $this->streamsStateMachine->apply($stream, 'extract_sound');
            ($this->processor)(new ExtractSound($stream, $command->getJobId()));
        } catch (ProcessorException $exception) {
            $stream->markAsExtractSoundFailed();
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
