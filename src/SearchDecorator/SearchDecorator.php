<?php

declare(strict_types=1);

namespace App\SearchDecorator;

use App\SearchDecorator\Filter\DateFilter;
use App\SearchDecorator\Filter\SearchFilter;
use App\SearchDecorator\Filter\StatusFilter;
use App\SearchDecorator\Query\SearchAbstract;

class SearchDecorator
{
    /**
     * @var array|string[]
     */
    private array $searchQueries = [
        StatusFilter::class,
        SearchFilter::class,
        DateFilter::class,
    ];

    private SearchAbstract $search;

    /**
     * @param array<string, mixed> $request
     */
    public function __construct(array $request)
    {
        $this->search = new Query\Search($request);

        foreach ($this->searchQueries as $q) {
            if (isset($request[$q::getName()])) {
                $instance = new $q($this->search);
                if ($instance instanceof SearchAbstract) {
                    $this->search = $instance;
                }
            }
        }
    }

    /**
     * @return SearchInterface
     */
    public function getSearch()
    {
        return $this->search;
    }
}
