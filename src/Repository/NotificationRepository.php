<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Notification>
 *
 * @method Notification|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Notification[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @method Notification|null find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method Notification[]    findAll()
 */
class NotificationRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }
}
