<?php

declare(strict_types=1);

namespace App\Service;

use FOS\ElasticaBundle\Elastica\Index;
use Psr\Log\LoggerInterface;

class ElasticsearchRefreshService
{
    public function __construct(
        private readonly Index $streamIndex,
        private readonly Index $notificationIndex,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * Force Elasticsearch to make recent changes visible immediately
     * This is needed when you create/update an entity and immediately want to search for it.
     */
    public function refreshStreamIndex(): void
    {
        try {
            $this->streamIndex->refresh();
        } catch (\Exception $e) {
            $this->logger->warning('Failed to refresh Elasticsearch stream index', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Force Elasticsearch to make recent notification changes visible immediately.
     */
    public function refreshNotificationIndex(): void
    {
        try {
            $this->notificationIndex->refresh();
        } catch (\Exception $e) {
            $this->logger->warning('Failed to refresh Elasticsearch notification index', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
