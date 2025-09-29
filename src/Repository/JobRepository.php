<?php

namespace App\Repository;

use App\Entity\Job;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Job>
 *
 * @method Job|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Job[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Job      find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Job      findOneBy(array $criteria, ?array $orderBy = null)
 * @method Job[]    findAll()
 */
class JobRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }

    /**
     * @param Uuid $jobId
     * @return Job|null
     */
    public function findByJobId(Uuid $jobId): ?Job
    {
        return $this->findOneBy(['id' => $jobId]);
    }
}
