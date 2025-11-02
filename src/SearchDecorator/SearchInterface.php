<?php

declare(strict_types=1);

namespace App\SearchDecorator;

use Elastica\Param;

interface SearchInterface
{
    /**
     * @return array<string, array{value:mixed, query:array<Param>}>
     */
    public function getQueries(): array;

    /**
     * @return array<string, mixed>
     */
    public function getRequest(): array;

    /** @return array<string, array{value:mixed, query:array<Param>}> */
    public function getOptionalQueries(): array;
}
