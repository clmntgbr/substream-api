<?php

declare(strict_types=1);

namespace App\Presentation\Controller\Stream;

use App\Application\Stream\Command\DeleteStreamCommand;
use App\Domain\Stream\Entity\Stream;
use App\Domain\Stream\Repository\StreamRepository;
use App\Infrastructure\RealTime\Mercure\MercurePublisherInterface;
use App\Shared\Application\Bus\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteStreamController extends AbstractController
{
    public function __construct(
        private StreamRepository $streamRepository,
        private CommandBusInterface $commandBus,
        private MercurePublisherInterface $mercurePublisher,
    ) {
    }

    public function __invoke(Stream $stream): JsonResponse
    {
        $stream->markAsDeleted();

        $this->streamRepository->saveAndFlush($stream);

        $this->commandBus->dispatch(new DeleteStreamCommand($stream->getId()));
        $this->mercurePublisher->refreshStreams($stream->getUser(), self::class);

        return new JsonResponse([
            'success' => true,
        ], JsonResponse::HTTP_OK);
    }
}
