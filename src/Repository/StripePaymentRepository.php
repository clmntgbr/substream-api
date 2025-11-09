<?php

namespace App\Repository;

use App\Entity\StripePayment;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<StripePayment>
 *
 * @method StripePayment|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method StripePayment[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method StripePayment|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method StripePayment[]    findAll()
 */
class StripePaymentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StripePayment::class);
    }

    public function findByUuid(Uuid $id): ?StripePayment
    {
        return $this->findOneBy(['id' => $id]);
    }
}
