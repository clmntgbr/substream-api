<?php

declare(strict_types=1);

namespace App\SearchDecorator\Filter;

use App\SearchDecorator\Query\SearchAbstract;
use Elastica\Query\Range;

class DateFilter extends SearchAbstract
{
    public static function getName(): string
    {
        return 'createdAt';
    }

    /**
     * @return array<\Elastica\Param>
     */
    public function getQuery(): array
    {
        $value = $this->getValue();

        if (null === $value || !is_array($value)) {
            return [];
        }

        $before = $value['before'] ?? null;
        $after = $value['after'] ?? null;

        if (null === $before && null === $after) {
            return [];
        }

        $rangeParams = [];

        if (null !== $before) {
            $beforeTimestamp = $this->parseDate($before);
            if (null !== $beforeTimestamp) {
                $rangeParams['lte'] = $beforeTimestamp;
            }
        }

        if (null !== $after) {
            $afterTimestamp = $this->parseDate($after);
            if (null !== $afterTimestamp) {
                $rangeParams['gte'] = $afterTimestamp;
            }
        }

        if (empty($rangeParams)) {
            return [];
        }

        $range = new Range('createdAt', $rangeParams);

        return [$range];
    }

    private function parseDate(string $date): ?string
    {
        $date = str_replace(' ', '+', $date);

        try {
            $dateTime = new \DateTime($date);

            return $dateTime->format('Y-m-d\TH:i:s\Z');
        } catch (\Exception $e) {
            return null;
        }
    }
}
