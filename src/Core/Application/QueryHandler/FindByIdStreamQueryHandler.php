<?php

namespace App\Core\Application\QueryHandler;

use App\Core\Application\Query\FindByIdStreamQuery;
use App\Entity\Stream;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FindByIdStreamQueryHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private \App\Core\Application\Mapper\Stream\StreamMapper $mapper
    ) {
    }

    public function __invoke(FindByIdStreamQuery $query): array
    {
        $parameter = $query->id?->value();
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('e')
        ->from(Stream::class, 'e')
        ->where('e.id = :parameter')
        ->setParameter('parameter', $parameter);

        $result = $qb->getQuery()->getResult();

        if (!$result) {
            throw new \Exception('Not found');
        }

        $data = array_map(
            fn ($entity) => $this->mapper->toArray($this->mapper->fromEntity($entity)),
            $result
        );

        return $data;
    }
}
