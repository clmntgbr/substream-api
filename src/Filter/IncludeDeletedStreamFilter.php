<?php

// src/Filter/RegexpFilter.php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ParameterNotFound;
use App\Enum\StreamStatusEnum;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class IncludeDeletedStreamFilter implements FilterInterface
{
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $parameter = $context['parameter'] ?? null;
        $value = $parameter?->getValue();

        if ($value instanceof ParameterNotFound) {
            return;
        }

        if ('true' === $value) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.status != :deleted', $alias))
            ->setParameter('deleted', StreamStatusEnum::DELETED->value);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'deleted' => [
                'property' => 'deleted',
                'type' => Type::BUILTIN_TYPE_BOOL,
            ],
        ];
    }
}
