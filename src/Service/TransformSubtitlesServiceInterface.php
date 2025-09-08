<?php

namespace App\Service;

use App\Entity\Stream;

interface TransformSubtitlesServiceInterface
{
    public function transformSubtitles(Stream $stream): void;
}
