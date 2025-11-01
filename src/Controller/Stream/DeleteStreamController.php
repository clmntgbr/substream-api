<?php

declare(strict_types=1);

namespace App\Controller\Stream;

use App\Entity\Stream;
use App\Repository\StreamRepository;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteStreamController extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(Stream $stream)
    {
        $stream->markAsDeleted();

        // API Platform controllers don't have doctrine_transaction middleware, need explicit flush
        $this->streamRepository->saveAndFlush($stream);

        return $stream;
    }
}
