<?php

declare(strict_types=1);

namespace App\Core\Domain\Task\Repository;

use App\Core\Domain\Task\Entity\Task;
use App\Shared\Domain\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends AbstractRepository<Task>
 *
 * @method Task|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Task[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Task|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
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
