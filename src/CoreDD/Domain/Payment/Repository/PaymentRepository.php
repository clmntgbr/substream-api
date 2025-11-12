<?php

namespace App\CoreDD\Domain\Payment\Repository;

use App\CoreDD\Domain\Payment\Entity\Payment;
use App\Shared\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Payment>
 *
 * @method Payment|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Payment[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Payment|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Payment[]    findAll()
 */
class PaymentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payment::class);
    }

    public function findByUuid(Uuid $id): ?Payment
    {
        return $this->findOneBy(['id' => $id]);
    }
}
