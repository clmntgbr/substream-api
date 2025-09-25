<?php

declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Core\Application\Command\CreateStreamCommand;
use App\Core\Domain\Aggregate\StreamModel;
use App\Core\Application\Mapper\Stream\StreamMapper;
use App\Entity\Stream;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private StreamMapper $mapper
    ) {
    }

    public function __invoke(CreateStreamCommand $command): StreamModel
    {
        $entity = Stream::create(
            fileName: $command->fileName?->value(),
            originalFileName: $command->originalFileName?->value(),
            url: $command->url?->value(),
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->mapper->fromEntity($entity);
    }
}
