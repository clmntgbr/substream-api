<?php

declare(strict_types=1);

namespace App\Core\Application\Trait;

use App\Entity\Job;
use App\Entity\Stream;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use App\Enum\StreamStatusEnum;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Workflow\WorkflowInterface;

trait JobTrait
{
    private Job $job;
    private JobRepository $jobRepository;

    public function findByJobId(Uuid $jobId): Job
    {
        $this->job = $this->jobRepository->findByJobId($jobId);

        return $this->job;
    }
    
    public function markJobAsSuccess(): void
    {
        $this->job->setStatus(JobStatusEnum::SUCCESS);
        $this->jobRepository->save($this->job, true);
    }
    
    public function markJobAsFailure(): void
    {
        $this->job->setStatus(JobStatusEnum::FAILURE);
        $this->jobRepository->save($this->job, true);
    }
    
    public function markJobAsRunning(): void
    {
        $this->job->setStatus(JobStatusEnum::RUNNING);
        $this->jobRepository->save($this->job, true);
    }    
}
