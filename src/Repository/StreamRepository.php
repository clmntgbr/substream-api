<?php

namespace App\Repository;

use App\Entity\Stream;
use App\Enum\StreamStatusEnum;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\NilUuid;

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

    /**
     * @return \Generator<Stream>
     */
    public function findDelayedStreams(): \Generator
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.status IN (:status)')
            ->andWhere('s.id > :lastId')
            ->andWhere('s.updatedAt < :updatedAt');

        $query = $qb->getQuery();

        do {
            $parameters = [
                'status' => [
                    StreamStatusEnum::EXTRACTING_SOUND_PROCESSING->value,
                    StreamStatusEnum::GENERATING_SUBTITLES_PROCESSING->value,
                    StreamStatusEnum::TRANSFORMING_SUBTITLE_PROCESSING->value,
                    StreamStatusEnum::TRANSFORMING_VIDEO_PROCESSING->value,
                    StreamStatusEnum::GENERATING_VIDEO_PROCESSING->value,
                ],
                'updatedAt' => new \DateTimeImmutable('2 hours ago'),
                'lastId' => $lastId ?? new NilUuid(),
            ];

            /** @var Stream[] $results */
            $results = $query->execute($parameters);

            foreach ($results as $stream) {
                $lastId = $stream->getId();
                yield $stream;
            }
        } while (false === empty($results));
    }
}
