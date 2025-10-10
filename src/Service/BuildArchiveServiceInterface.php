<?php

namespace App\Service;

use App\Entity\Stream;
use Symfony\Component\HttpFoundation\File\File;

interface BuildArchiveServiceInterface
{
    public function build(Stream $stream): File;
}
