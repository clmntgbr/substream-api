<?php

namespace App\Repository;

use App\Entity\Subscription;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Subscription>
 *
 * @method Subscription|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Subscription[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Subscription|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Subscription[]    findAll()
 */
class SubscriptionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findByUuid(Uuid $id): ?Subscription
    {
        return $this->findOneBy(['id' => $id]);
    }
}
