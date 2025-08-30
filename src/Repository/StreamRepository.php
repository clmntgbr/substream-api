<?php

namespace App\Repository;

use App\Entity\Stream;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Stream>
 */
class StreamRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stream::class);
    }
}
