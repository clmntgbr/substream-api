<?php

declare(strict_types=1);

namespace App\Core\Domain\Stream\Repository;

use App\Core\Domain\Stream\Entity\Stream;
use App\Shared\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Stream>
 *
 * @method Stream|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Stream[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Stream|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
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
