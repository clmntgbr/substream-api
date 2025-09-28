<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Job;
use App\Enum\JobStatusEnum;
use Doctrine\ORM\EntityManagerInterface;

class JobLifecycleService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobContextService $jobContextService,
    ) {
    }

    public function getCurrentJob(): ?Job
    {
        $jobId = $this->jobContextService->getCurrentJobId();

        if (!$jobId) {
            return null;
        }

        return $this->entityManager->find(Job::class, $jobId);
    }

    public function markJobAsRunning(): ?Job
    {
        $job = $this->getCurrentJob();

        if ($job) {
            $job->setStatus(JobStatusEnum::RUNNING);
            $this->entityManager->flush();
        }

        return $job;
    }

    public function markJobAsSuccess(array $metadata = []): ?Job
    {
        $job = $this->getCurrentJob();

        if ($job) {
            $job->setStatus(JobStatusEnum::SUCCESS);
            if (!empty($metadata)) {
                $job->setMetadata($metadata);
            }
            $this->entityManager->flush();
        }

        return $job;
    }

    public function markJobAsFailure(\Throwable $exception): ?Job
    {
        $job = $this->getCurrentJob();

        if ($job) {
            $job->setStatus(JobStatusEnum::FAILURE);
            $job->setErrorMessage($exception->getMessage());
            $job->setErrorTrace($exception->getTraceAsString());
            $this->entityManager->flush();
        }

        return $job;
    }

    public function executeWithJobTracking(callable $callback, array $successMetadata = [], ?callable $metadataCallback = null): mixed
    {
        $job = $this->markJobAsRunning();

        try {
            $result = $callback();

            if ($job) {
                // Si une fonction de callback est fournie, l'utiliser pour générer les métadonnées
                if ($metadataCallback) {
                    $successMetadata = array_merge($successMetadata, $metadataCallback($result));
                }
                $this->markJobAsSuccess($successMetadata);
            }

            return $result;
        } catch (\Throwable $exception) {
            if ($job) {
                $this->markJobAsFailure($exception);
            }
            throw $exception;
        }
    }
}
