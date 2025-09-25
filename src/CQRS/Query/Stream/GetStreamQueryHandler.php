<?php

declare(strict_types=1);

namespace App\CQRS\Query\Stream;

use App\CQRS\Query\QueryResult;
use App\Repository\StreamRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetStreamQueryHandler
{
    public function __construct(
        private StreamRepository $streamRepository
    ) {
    }

    public function __invoke(GetStreamQuery $query): QueryResult
    {
        try {
            $stream = $this->streamRepository->findOneBy(['id' => $query->streamId]);
            
            if (!$stream) {
                return new QueryResult(null, 'Stream not found');
            }

            return new QueryResult($stream);
        } catch (\Throwable $exception) {
            return new QueryResult(null, $exception->getMessage());
        }
    }
}
