<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\GetVideoByUrlCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Job;
use App\Entity\Stream;
use App\Enum\JobStatusEnum;
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
        JobContextService $jobContextService,
        JobRepository $jobRepository,
    ) {
        parent::__construct($jobContextService, $jobRepository);
    }

    public function __invoke(GetVideoByUrlCommand $command): CreateStreamModel
    {
    }
}
