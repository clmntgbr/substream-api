<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Application\Mapper\CreateStreamMapperInterface;
use App\Core\Domain\Aggregate\CreateStreamModel;
use App\Entity\Job;
use App\Entity\Stream;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use App\Repository\StreamRepository;
use App\Service\JobContextService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

abstract class CommandHandlerAbstract
{
    public function __construct(
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    protected function getCurrentJob(): ?Job
    {
        $jobId = $this->jobContextService->getCurrentJobId();
        
        if (null === $jobId) {
            return null;
        }

        return $this->jobRepository->find($jobId);
    }

    protected function markJobAsFailure(\Throwable $exception): void
    {
        $job = $this->getCurrentJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $job->setErrorMessage($exception->getMessage());
            $job->setErrorTrace($exception->getTraceAsString());
            $this->jobRepository->save($job, true);
        }
    }

    protected function markJobAsSuccess(): void
    {
        $job = $this->getCurrentJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::SUCCESS);
            $this->jobRepository->save($job, true);
        }
    }

    protected function markJobAsRunning(): void
    {
        $job = $this->getCurrentJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::RUNNING);
            $this->jobRepository->save($job, true);
        }
    }
}
