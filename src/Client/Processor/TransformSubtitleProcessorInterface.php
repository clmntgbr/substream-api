<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformSubtitle;

interface TransformSubtitleProcessorInterface
{
    public function __invoke(TransformSubtitle $dto): void;
}
