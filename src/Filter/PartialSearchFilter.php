<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

final class PartialSearchFilter implements FilterInterface
{
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $parameter = $context['parameter'];
        $value = $parameter->getValue();
        $property = $parameter->getProperty();
        
        if (null === $property || null === $value) {
            return;
        }
        
        $alias = $queryBuilder->getRootAliases()[0];
        $field = $alias.'.'.$property;
        $parameterName = $queryNameGenerator->generateParameterName($property);
        
        $queryBuilder
            ->andWhere($queryBuilder->expr()->like($field, ':'.$parameterName))
            ->setParameter($parameterName, '%'.$value.'%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [];
    }
}

