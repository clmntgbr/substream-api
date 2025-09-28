<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Stream;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler extends CommandHandlerAbstract
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CreateStreamMapperInterface $createStreamMapper,
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
    }

    public function __invoke(CreateStreamCommand $command): CreateStreamModel
    {
        try {
            $stream = Stream::create(
                id: $command->streamId,
                user: $command->user,
                fileName: $command->fileName,
                originalFileName: $command->originalFileName,
                url: $command->url,
                mimeType: $command->mimeType,
                size: $command->size,
            );

            $this->streamRepository->save($stream, true);
            $this->markJobAsSuccess();

            return $this->createStreamMapper->fromEntity($stream);
        } catch (\Throwable $exception) {
            $this->markJobAsFailure($exception->getMessage());
            throw $exception;
        }
    }
}
