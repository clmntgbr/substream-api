<?php

declare(strict_types=1);

namespace App\CQRS\Trait;

use App\CQRS\Service\JobLifecycleService;

trait JobTrackingTrait
{
    protected JobLifecycleService $jobLifecycleService;

    public function setJobLifecycleService(JobLifecycleService $jobLifecycleService): void
    {
        $this->jobLifecycleService = $jobLifecycleService;
    }

    protected function executeWithJobTracking(callable $callback, array $successMetadata = [], ?callable $metadataCallback = null): mixed
    {
        return $this->jobLifecycleService->executeWithJobTracking($callback, $successMetadata, $metadataCallback);
    }
}
