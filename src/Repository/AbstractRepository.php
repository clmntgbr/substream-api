<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Uid\Uuid;

/**
 * @template T of object
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    /**
     * @param T $entity
     *
     * @return T
     */
    public function refresh(object $entity): object
    {
        $this->getEntityManager()->refresh($entity);

        return $entity;
    }

    /**
     * @return T|null
     */
    public function findByUuid(Uuid $id): ?object
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * @param T $entity
     */
    public function delete(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param T $entity
     */
    public function deleteAndFlush(object $entity): void
    {
        $this->delete($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @param T $entity
     */
    public function save(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param T $entity
     */
    public function saveAndFlush(object $entity): void
    {
        $this->save($entity);
        $this->getEntityManager()->flush();
    }

    public function saveAll(array $entities): void
    {
        foreach ($entities as $entity) {
            $this->save($entity);
        }
    }

    public function saveAllChunks(iterable $entities, int $chunkSize = 50): void
    {
        if ($chunkSize <= 0) {
            throw new \ValueError(__METHOD__.': Argument #2 ($chunkSize) must be greater than 0');
        }

        $em = $this->getEntityManager();

        $chunks = iterator_chunk($entities, $chunkSize);
        foreach ($chunks as $chunk) {
            foreach ($chunk as $entity) {
                if (!$em->contains($entity)) {
                    $em->persist($entity);
                }
            }

            $em->flush();
            $em->clear();
        }
    }
}
