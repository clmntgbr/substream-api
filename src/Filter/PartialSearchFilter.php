<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;

final class PartialSearchFilter implements FilterInterface
{
    public function apply(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $parameter = $context['parameter'];
        $value = $parameter->getValue();
        $property = $parameter->getProperty();

        if (null === $property || null === $value) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];
        $field = $alias.'.'.$property;
        $values = is_array($value) ? $value : explode(',', $value);

        $includeExpressions = [];
        $excludeExpressions = [];

        foreach ($values as $val) {
            $val = trim($val);

            if (empty($val)) {
                continue;
            }

            if (str_starts_with($val, '!')) {
                $excludeExpressions[] = $this->createExcludeExpression(
                    $queryBuilder,
                    $queryNameGenerator,
                    $field,
                    $property,
                    substr($val, 1)
                );
            } else {
                $includeExpressions[] = $this->createIncludeExpression(
                    $queryBuilder,
                    $queryNameGenerator,
                    $field,
                    $property,
                    $val
                );
            }
        }

        $this->applyFilters($queryBuilder, $includeExpressions, $excludeExpressions);
    }

    private function createIncludeExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $field,
        string $property,
        string $value,
    ): Comparison {
        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder->setParameter($parameterName, '%'.$value.'%');

        return $queryBuilder->expr()->like($field, ':'.$parameterName);
    }

    private function createExcludeExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $field,
        string $property,
        string $value,
    ): ?Comparison {
        if (empty($value)) {
            return null;
        }

        $parameterName = $queryNameGenerator->generateParameterName($property);
        $queryBuilder->setParameter($parameterName, '%'.$value.'%');

        return $queryBuilder->expr()->notLike($field, ':'.$parameterName);
    }

    private function applyFilters(
        QueryBuilder $queryBuilder,
        array $includeExpressions,
        array $excludeExpressions,
    ): void {
        if (!empty($includeExpressions)) {
            $queryBuilder->andWhere($queryBuilder->expr()->orX(...$includeExpressions));
        }

        foreach (array_filter($excludeExpressions) as $expression) {
            $queryBuilder->andWhere($expression);
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}
