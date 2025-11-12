<?php

declare(strict_types=1);

namespace App\Repository;

use App\CoreDD\Domain\User\Entity\User;
use App\SearchDecorator\SearchInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ElasticaNotificationRepository extends AbstractElasticaRepository
{
    public function __construct(PaginatedFinderInterface $finder)
    {
        parent::__construct($finder);
    }

    public function getSearchQuery(SearchInterface $search, ?User $user = null): Query
    {
        $query = parent::getSearchQuery($search, $user);

        $request = $search->getRequest();

        return $query;
    }
}
