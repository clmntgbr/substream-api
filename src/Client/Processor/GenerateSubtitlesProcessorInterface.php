<?php

namespace App\Client\Processor;

use App\Dto\Processor\GenerateSubtitles;

interface GenerateSubtitlesProcessorInterface
{
    public function __invoke(GenerateSubtitles $dto): void;
}
