<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\SearchDecorator\SearchInterface;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;

class ElasticaNotificationRepository extends AbstractElasticaRepository
{
    public function __construct(PaginatedFinderInterface $finder)
    {
        parent::__construct($finder);
    }

    public function getSearchQuery(?User $user = null, SearchInterface $search): Query
    {
        $query = parent::getSearchQuery($user, $search);

        $request = $search->getRequest();

        return $query;
    }
}
