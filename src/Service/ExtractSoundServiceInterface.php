<?php

namespace App\Service;

use App\Entity\Stream;

interface ExtractSoundServiceInterface
{
    public function extractSound(Stream $stream): void;
}
