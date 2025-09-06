<?php

namespace App\Service;

use App\Entity\Stream;

interface GenerateSubtitlesServiceInterface
{
    public function generateSubtitles(Stream $stream): void;
}
