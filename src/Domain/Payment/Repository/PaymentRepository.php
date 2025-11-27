<?php

namespace App\Domain\Payment\Repository;

use App\Domain\Payment\Entity\Payment;
use App\Domain\User\Entity\User;
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

    public function getPaymentStatsByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('SUM(p.amount) as amount, COUNT(p.id) as count')
            ->join('p.subscription', 's')
            ->where('s IN (:subscriptions)')
            ->setParameter('subscriptions', $user->getSubscriptions());

        return $qb->getQuery()->getSingleResult();
    }
}
