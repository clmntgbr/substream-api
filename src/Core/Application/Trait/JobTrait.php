<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use App\Entity\Job;
use App\Enum\JobStatusEnum;
use App\Exception\JobNotFoundException;
use App\Repository\JobRepository;
use App\Service\JobContextService;

trait JobTrait
{
    public function __construct(
        private JobRepository $jobRepository,
        private JobContextService $jobContextService,
    ) {
    }

    public function getJob(): ?Job
    {
        $jobId = $this->jobContextService->getCurrentJobId();
        if (null === $jobId) {
            dd('jobId is null');
            throw new JobNotFoundException();
        }

        $job = $this->jobRepository->find($jobId);
        if (null === $job) {
            dd('job not found');
            throw new JobNotFoundException();
        }

        return $job;
    }

    protected function markJobAsSuccess(): void
    {
        dump('markJobAsSuccess');
        $job = $this->getJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::SUCCESS);
            $this->jobRepository->save($job, true);
        }
    }

    protected function markJobAsFailure(string $errorMessage): void
    {
        dump('markJobAsFailure');
        $job = $this->getJob();
        if ($job instanceof Job) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $job->setErrorMessage($errorMessage);
            $this->jobRepository->save($job, true);
        }
    }
}
