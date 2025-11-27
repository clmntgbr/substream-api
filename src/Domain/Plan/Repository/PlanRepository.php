<?php

declare(strict_types=1);

namespace App\Domain\Plan\Repository;

use App\Domain\Plan\Entity\Plan;
use App\Shared\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Plan>
 *
 * @method Plan|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Plan[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Plan|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Plan[] findAll()
 */
class PlanRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }
}
