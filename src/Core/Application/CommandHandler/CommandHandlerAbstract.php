<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Entity\Job;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use App\Service\JobContextService;

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
