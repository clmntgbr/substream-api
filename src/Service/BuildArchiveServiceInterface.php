<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Domain\Stream\Entity\Stream;
use Symfony\Component\HttpFoundation\File\File;

interface BuildArchiveServiceInterface
{
    public function build(Stream $stream): File;
}
