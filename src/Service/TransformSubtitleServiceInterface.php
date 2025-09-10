<?php

namespace App\Service;

use App\Entity\Stream;

interface TransformSubtitleServiceInterface
{
    public function transformSubtitle(Stream $stream): void;
}
