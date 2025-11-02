<?php

declare(strict_types=1);

namespace App\SearchDecorator\Filter;

use App\SearchDecorator\Query\SearchAbstract;
use Elastica\Query\BoolQuery;
use Elastica\Query\Wildcard;

class SearchFilter extends SearchAbstract
{
    public static function getName(): string
    {
        return 'search';
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

        $searchString = trim((string) $values[0]);

        if (empty($searchString)) {
            return [];
        }

        $bool = new BoolQuery();

        $searchFields = [
            'originalFileName.keyword',
            'fileName.keyword',
            'originalFileName',
            'fileName',
        ];

        if (str_contains($searchString, ' ')) {
            $words = explode(' ', $searchString);
            foreach ($words as $word) {
                if (empty($word)) {
                    continue;
                }
                $wordBool = new BoolQuery();
                foreach ($searchFields as $field) {
                    $wildcard = new Wildcard($field, '*'.strtolower($word).'*');
                    $wordBool->addShould($wildcard);
                }
                $bool->addMust($wordBool);
            }
        } else {
            foreach ($searchFields as $field) {
                $wildcard = new Wildcard($field, '*'.strtolower($searchString).'*');
                $bool->addShould($wildcard);
            }
        }

        return [$bool];
    }
}
