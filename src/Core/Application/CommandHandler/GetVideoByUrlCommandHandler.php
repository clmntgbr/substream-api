<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Client\Processor\GetVideoByUrlProcessorInterface;
use App\Core\Application\Command\GetVideoByUrlCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Dto\GetVideoByUrl;
use App\Entity\Job;
use App\Entity\Stream;
use App\Enum\JobStatusEnum;
use App\Enum\StreamStatusEnum;
use App\Exception\ProcessorException;
use App\Exception\StreamNotFoundException;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetVideoByUrlCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CreateStreamMapperInterface $createStreamMapper,
        private GetVideoByUrlProcessorInterface $processor,
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
    }

    public function __invoke(GetVideoByUrlCommand $command): void
    {
        $stream = $this->streamRepository->find($command->streamId);

        if ($stream === null) {
            throw new StreamNotFoundException();
        }

        try {
            ($this->processor)(new GetVideoByUrl($stream));
        } catch (ProcessorException $exception) {
            $stream->markAsUploadFailed();
            $this->markJobAsFailure($exception);
        } finally {
            $this->streamRepository->save($stream);
        }
    }
}
