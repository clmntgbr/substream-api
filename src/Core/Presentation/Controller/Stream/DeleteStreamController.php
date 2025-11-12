<?php

declare(strict_types=1);

namespace App\Core\Presentation\Controller\Stream;

use App\Core\Domain\Stream\Entity\Stream;
use App\Core\Domain\Stream\Repository\StreamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteStreamController extends AbstractController
{
    public function __construct(
        private StreamRepository $streamRepository,
    ) {
    }

    public function __invoke(Stream $stream): Stream
    {
        $stream->markAsDeleted();

        $this->streamRepository->saveAndFlush($stream);

        return $stream;
    }
}
