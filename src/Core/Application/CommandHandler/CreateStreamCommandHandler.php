<?php
/**
 * Class CreateStreamCommandHandler*.
 *
 * @see \App\Core\Application\Command\CreateStreamCommand*
 */
declare(strict_types=1);

namespace App\Core\Application\CommandHandler;

use App\Entity\Stream;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStreamCommandHandlerHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private \App\Core\Application\Mapper\Stream\StreamMapper $mapper
    ) {
    }

    public function __invoke(\App\Core\Application\Command\CreateStreamCommand $command): \App\Core\Domain\Aggregate\StreamModel
    {
        $entity = new Stream(
            fileName: $command->fileName?->value(),
            originalFileName: $command->originalFileName?->value(),
            url: $command->url?->value(),
        );

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->mapper->fromEntity($entity);
    }
}
