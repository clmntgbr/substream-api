<?php

declare(strict_types=1);

namespace App\SearchDecorator\Aggregation;

use Elastica\Aggregation\AbstractAggregation;
use Elastica\Query;

interface AggregationInterface
{
    public function getAggregation(): AbstractAggregation;

    public function setAggregation(Query $query): void;
}
