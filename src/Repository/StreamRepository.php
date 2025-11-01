<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Stream;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Stream>
 *
 * @method Stream|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Stream[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Stream      find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Stream      findOneBy(array $criteria, ?array $orderBy = null)
 * @method Stream[]    findAll()
 */
class StreamRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stream::class);
    }

    public function findByUuid(Uuid $id): ?Stream
    {
        return $this->findOneBy(['id' => $id]);
    }
}
