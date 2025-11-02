<?php

declare(strict_types=1);

namespace App\SearchDecorator\Filter;

use App\SearchDecorator\Query\SearchAbstract;
use Elastica\Query\BoolQuery;
use Elastica\Query\Term;

class StatusFilter extends SearchAbstract
{
    public static function getName(): string
    {
        return 'statusFilter';
    }

    /**
     * @return array<\Elastica\Param>
     */
    public function getQuery(): array
    {
        $value = $this->getValue();

        if (null === $value || '' === $value) {
            return [];
        }

        $values = is_array($value) ? $value : explode(',', (string) $value);

        $includeQueries = [];
        $excludeQueries = [];

        foreach ($values as $val) {
            $val = trim($val);

            if (empty($val)) {
                continue;
            }

            if (str_starts_with($val, '!')) {
                $status = substr($val, 1);
                if (!empty($status)) {
                    $excludeQueries[] = new Term(['filterStatus' => $status]);
                }
            } else {
                $includeQueries[] = new Term(['filterStatus' => $val]);
            }
        }

        if (empty($includeQueries) && empty($excludeQueries)) {
            return [];
        }

        $bool = new BoolQuery();

        if (!empty($includeQueries)) {
            $includeBool = new BoolQuery();
            foreach ($includeQueries as $includeQuery) {
                $includeBool->addShould($includeQuery);
            }
            $bool->addMust($includeBool);
        }

        foreach ($excludeQueries as $excludeQuery) {
            $bool->addMustNot($excludeQuery);
        }

        return [$bool];
    }
}
