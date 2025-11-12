<?php

declare(strict_types=1);

namespace App\CoreDD\Presentation\Controller\Stream;

use App\CoreDD\Domain\Stream\Entity\Stream;
use App\CoreDD\Domain\Stream\Repository\StreamRepository;
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
