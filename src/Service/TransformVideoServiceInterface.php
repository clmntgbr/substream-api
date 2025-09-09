<?php

namespace App\Service;

use App\Entity\Stream;

interface TransformVideoServiceInterface
{
    public function transformVideo(Stream $stream): void;
}
