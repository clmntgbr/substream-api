<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use App\Entity\Job;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use App\Service\JobContextService;
use Symfony\Component\Uid\Uuid;

trait JobTrait
{
    public function __construct(
        private JobContextService $jobContextService,
        private JobRepository $jobRepository,
    ) {
    }

    protected function getJob(Uuid $jobId): ?Job
    {
        $job = $this->jobRepository->find($jobId);
        if (null === $job) {
            return null;
        }

        $this->jobContextService->setCurrentJobId($jobId);

        return $job;
    }

    protected function getCurrentJob(): ?Job
    {
        $jobId = $this->jobContextService->getCurrentJobId();

        if (null === $jobId) {
            return null;
        }

        return $this->jobRepository->find($jobId);
    }

    protected function markJobAsFailure(?string $errorMessage = null): void
    {
        $job = $this->getCurrentJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $job->setErrorMessage($errorMessage);
            $job->setErrorTrace($errorMessage);
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
