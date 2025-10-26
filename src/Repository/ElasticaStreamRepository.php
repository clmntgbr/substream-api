<?php

declare(strict_types=1);

namespace App\Repository;

use App\SearchDecorator\SearchInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ElasticaStreamRepository extends AbstractElasticaRepository
{
    public function __construct(PaginatedFinderInterface $finder)
    {
        parent::__construct($finder);
    }

    public function getSearchQuery(SearchInterface $search): Query
    {
        $query = parent::getSearchQuery($search);

        $request = $search->getRequest();

        return $query;
    }
}
