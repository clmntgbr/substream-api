<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformSubtitles;

interface TransformSubtitlesProcessorInterface
{
    public function __invoke(TransformSubtitles $dto): void;
}
