<?php

declare(strict_types=1);

namespace App\CoreDD\Domain\SocialAccount\Repository;

use App\CoreDD\Domain\SocialAccount\Entity\SocialAccount;
use App\Shared\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<SocialAccount>
 *
 * @method SocialAccount|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method SocialAccount[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method SocialAccount|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method SocialAccount[]    findAll()
 */
class SocialAccountRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocialAccount::class);
    }
}
