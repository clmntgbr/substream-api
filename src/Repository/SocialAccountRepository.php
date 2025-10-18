<?php

namespace App\Repository;

use App\Entity\SocialAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<SocialAccount>
 *
 * @method SocialAccount|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SocialAccount[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method SocialAccount      find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method SocialAccount      findOneBy(array $criteria, ?array $orderBy = null)
 * @method SocialAccount[]    findAll()
 */
class SocialAccountRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialAccount::class);
    }
}
