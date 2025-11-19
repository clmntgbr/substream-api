<?php

declare(strict_types=1);

namespace App\Infrastructure\Stream\Archive;

use App\Domain\Stream\Entity\Stream;
use Symfony\Component\HttpFoundation\File\File;

interface BuildArchiveServiceInterface
{
    public function build(Stream $stream): File;
}
