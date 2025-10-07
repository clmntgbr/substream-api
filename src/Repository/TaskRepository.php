<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Task>
 *
 * @method Task|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method Task[]    findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Task      find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Task      findOneBy(array $criteria, ?array $orderBy = null)
 * @method Task[]    findAll()
 */
class TaskRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function findByUuid(Uuid $id): ?Task
    {
        return $this->findOneBy(['id' => $id]);
    }
}
