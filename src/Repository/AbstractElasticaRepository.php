<?php

declare(strict_types=1);

namespace App\Repository;

use App\Core\Domain\User\Entity\User;
use App\SearchDecorator\Aggregation\AggregationInterface;
use App\SearchDecorator\SearchInterface;
use Elastica\Query;
use Elastica\Query\AbstractQuery;
use Elastica\Query\BoolQuery;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use FOS\ElasticaBundle\Paginator\FantaPaginatorAdapter;
use FOS\ElasticaBundle\Repository;
use Pagerfanta\Pagerfanta;

class AbstractElasticaRepository extends Repository
{
    public function __construct(protected PaginatedFinderInterface $finder)
    {
    }

    /**
     * @return array{
     * total_items: int,
     * items_per_page: int,
     * current_page: int,
     * next_page: int|null,
     * total_pages: int,
     * results: Pagerfanta<object>,
     * aggregations: array<string, mixed>|null
     * }
     */
    public function search(SearchInterface $search, int $page = 1, int $limit = 15, ?User $user = null): array
    {
        $query = $this->getSearchQuery($search, $user);
        $results = $this->finder->findPaginated($query);
        $results->setMaxPerPage($limit);
        $results->setCurrentPage($page);
        $totalHits = $results->count();

        $totalPages = intval(ceil($totalHits / $limit));
        /** @var FantaPaginatorAdapter<object> $adapter */
        $adapter = $results->getAdapter();
        $aggs = $adapter->getAggregations();

        return [
            'total_items' => $totalHits,
            'items_per_page' => $limit,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'results' => $results,
            'next_page' => ($page + 1) <= $totalPages ? $page + 1 : null,
            'aggregations' => $aggs,
        ];
    }

    public function getSearchQuery(SearchInterface $search, ?User $user = null): Query
    {
        $queries = $search->getQueries();
        $bool = new BoolQuery();

        foreach ($queries as $searchQuery) {
            if (!$searchQuery['query']) {
                continue;
            }

            foreach ($searchQuery['query'] as $q) {
                /** @var AbstractQuery $q */
                /** @var array<string, mixed> $qArray */
                $qArray = $q->toArray();

                if (isset($qArray['wildcard']) && is_array($qArray['wildcard'])) {
                    foreach ($qArray['wildcard'] as $field => $wildcardData) {
                        if (is_array($wildcardData) && isset($wildcardData['value'])) {
                            $searchString = trim($wildcardData['value']);
                            if ('' === $searchString) {
                                continue;
                            }

                            $bool = $this->getWildCards($bool, $searchString, $field);
                            continue 2;
                        }
                    }
                }

                $bool->addMust($q);
            }
        }

        $optional = $search->getOptionalQueries();
        foreach ($optional as $sq) {
            foreach ($sq['query'] as $q) {
                if ($q instanceof AbstractQuery) {
                    $bool->addShould($q);
                }
            }
        }
        if (!empty($optional)) {
            $bool->setMinimumShouldMatch(0);
        }

        $request = $search->getRequest();

        $query = $this->addAggregations(new Query(), $request);

        if ($user instanceof User) {
            $userQuery = (new Query\Term())
                ->setTerm('userUuid', (string) $user->getId());

            $bool->addMust($userQuery);
        }

        $query->setQuery($bool);

        if (isset($request['order'])) {
            /** @var array<string, string> $requestOrder */
            $requestOrder = $request['order'];
            $query = $this->sortQuery($query, $requestOrder);
        } else {
            $query = $this->getDefaultSort($query);
        }

        return $query;
    }

    /**
     * @param array<string, string> $requestOrder
     */
    protected function sortQuery(Query $query, array $requestOrder): Query
    {
        if (!empty($requestOrder)) {
            foreach ($requestOrder as $field => $order) {
                $field = $this->normalizeSortField($field);
                $query->addSort([$field => strtolower($order)]);
            }
        } else {
            $query = $this->getDefaultSort($query);
        }

        return $query;
    }

    /**
     * Normalise le champ de tri (ajoute .keyword si nécessaire).
     */
    protected function normalizeSortField(string $field): string
    {
        $dateFields = ['startAt', 'createdAt', 'orderedAt', 'updatedAt'];
        $booleanFields = ['enabled'];

        if (!in_array($field, array_merge($dateFields, $booleanFields), true)) {
            $field .= '.keyword';
        }

        return $field;
    }

    /**
     * Retourne le tri par défaut (peut être surchargé dans les classes filles).
     */
    protected function getDefaultSort(Query $query): Query
    {
        $query->setSort(['createdAt' => 'desc']);

        return $query;
    }

    protected function getWildCards(BoolQuery $bool, string $searchString, string $field = 'name'): BoolQuery
    {
        if (false !== strpos($searchString, ' ')) {
            $tokens = preg_split('/\s+/', $searchString);
            if (false === $tokens) {
                $tokens = [];
            }

            $subBool = new BoolQuery();
            foreach ($tokens as $token) {
                if ('' !== $token) {
                    $subBool->addMust(new Query\Wildcard($field, '*'.$token.'*'));
                }
            }
            $bool->addMust($subBool);
        } else {
            $wildcardQuery = new Query\Wildcard($field, '*'.$searchString.'*');
            $bool->addMust($wildcardQuery);
        }

        return $bool;
    }

    /**
     * @param array<array<string>|float|int|string> $request
     */
    public function addAggregations(Query $query, array $request): Query
    {
        if (isset($request['aggregations']) && \is_array($request['aggregations'])) {
            foreach ($request['aggregations'] as $aggregationName) {
                $className = \sprintf('App\SearchDecorator\Aggregation\%s', $aggregationName);
                if (\class_exists($className)) {
                    /** @var AggregationInterface $aggregation */
                    $aggregation = new $className();

                    $aggregation->setAggregation($query);
                }
            }
        }

        return $query;
    }
}
