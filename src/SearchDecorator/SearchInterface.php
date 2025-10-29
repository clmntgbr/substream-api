<?php

declare(strict_types=1);

namespace App\SearchDecorator;

use Elastica\Query\AbstractQuery;

interface SearchInterface
{
    public function getQueries(): array;

    /**
     * @return array<string|int|float|string[]>
     */
    public function getRequest(): array;

    /** @return array<string, array{value:mixed, query:array<AbstractQuery>}> */
    public function getOptionalQueries(): array;
}
