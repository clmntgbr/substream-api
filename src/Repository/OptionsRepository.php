<?php

namespace App\Repository;

use App\Entity\Options;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Options>
 *
 * @method Options|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Options[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Options      find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Options      findOneBy(array $criteria, ?array $orderBy = null)
 * @method Options[]    findAll()
 */
class OptionsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Options::class);
    }
}
