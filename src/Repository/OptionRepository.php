<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Option;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Option>
 *
 * @method Option|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Option[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Option|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Option[]    findAll()
 */
class OptionRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Option::class);
    }
}
