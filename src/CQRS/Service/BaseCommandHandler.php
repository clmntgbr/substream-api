<?php

declare(strict_types=1);

namespace App\CQRS\Service;

abstract class BaseCommandHandler
{
    public function __construct(
        protected JobLifecycleService $jobLifecycleService
    ) {
    }

    protected function executeWithJobTracking(callable $callback, array $successMetadata = [], ?callable $metadataCallback = null): mixed
    {
        return $this->jobLifecycleService->executeWithJobTracking($callback, $successMetadata, $metadataCallback);
    }

    protected function getCurrentJob(): ?\App\Entity\Job
    {
        return $this->jobLifecycleService->getCurrentJob();
    }

    protected function markJobAsRunning(): ?\App\Entity\Job
    {
        return $this->jobLifecycleService->markJobAsRunning();
    }

    protected function markJobAsSuccess(array $metadata = []): ?\App\Entity\Job
    {
        return $this->jobLifecycleService->markJobAsSuccess($metadata);
    }

    protected function markJobAsFailure(\Throwable $exception): ?\App\Entity\Job
    {
        return $this->jobLifecycleService->markJobAsFailure($exception);
    }
}
