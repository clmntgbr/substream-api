<?php

declare(strict_types=1);

namespace App\SearchDecorator\Query;

use Elastica\Param;

class Search extends SearchAbstract
{
    /**
     * @param array<string, mixed> $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
        parent::__construct($this);
    }

    /**
     * @return array<string, array{value:mixed, query:array<Param>}>
     */
    public function getQueries(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    public static function getName(): string
    {
        return 'search';
    }

    /**
     * @return array<string, array{value:mixed, query:array<Param>}>
     */
    public function getOptionalQueries(): array
    {
        return [];
    }
}
