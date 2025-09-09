<?php

namespace App\Client\Processor;

use App\Dto\Processor\TransformVideo;

interface TransformVideoProcessorInterface
{
    public function __invoke(TransformVideo $dto): void;
}
