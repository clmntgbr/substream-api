<?php

declare(strict_types=1);

namespace App\SearchDecorator\Query;

use App\SearchDecorator\SearchInterface;
use Elastica\Param;

abstract class SearchAbstract implements SearchInterface
{
    protected SearchInterface $query;
    /**
     * @var array<string|int|float|string[]>
     */
    protected array $request;

    /**
     * @var array<Param>
     */
    protected array $elasticaRequest = [];

    /** @var array<Param> */
    protected array $optionalRequest = [];

    public function __construct(SearchInterface $query)
    {
        $this->query = $query;
        $this->request = $query->getRequest();
    }

    public function getValue(): array|string|int|float|bool|null
    {
        return $this->request[$this->getName()];
    }

    abstract public static function getName(): string;

    /**
     * Returns the list of all queries.
     *
     * @return array{ SearchInterface }|array{}
     */
    public function getQueries(): array
    {
        $queries = $this->query->getQueries();
        $queries[$this->getName()] = [
            'value' => $this->request[$this->getName()],
            'query' => $this->getQuery(),
        ];

        return $queries;
    }

    /**
     * @return array<Param>
     */
    public function getQuery(): array
    {
        return $this->elasticaRequest ?? [];
    }

    public function setQuery(Param $query): Param
    {
        return $this->elasticaRequest[0] = $query;
    }

    /**
     * @return array<string|int|float|string[]>
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @return array<string, array{value:mixed, query:array<Param>}>
     */
    public function getOptionalQueries(): array
    {
        $queries = $this->query->getOptionalQueries();
        $queries[$this::getName().'_opt'] = [
            'value' => $this->getValue(),
            'query' => $this->optionalRequest,
        ];

        return $queries;
    }

    protected function setOptional(Param $query): void
    {
        $this->optionalRequest[] = $query;
    }
}
