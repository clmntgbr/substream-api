<?php

declare(strict_types=1);

namespace App\CoreDD\Infrastructure\Search\Elasticsearch;

interface ElasticsearchRefresherInterface
{
    public function refreshStreamIndex(): void;

    public function refreshNotificationIndex(): void;
}
