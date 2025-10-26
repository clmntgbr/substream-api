<?php

declare(strict_types=1);

namespace App\SearchDecorator\Query;

class Search extends SearchAbstract
{
    /**
     * @var array<string, string>
     */
    protected array $request;

    /**
     * @param array<string, string> $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;
        parent::__construct($this);
    }

    /**
     * @return array<string, string>
     */
    public function getQueries(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getValues(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    public static function getName(): string
    {
        return 'search';
    }

    public function getOptionalQueries(): array
    {
        return [];
    }
}
