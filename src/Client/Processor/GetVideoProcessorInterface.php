<?php

namespace App\Client\Processor;

use App\Dto\GetVideo;

interface GetVideoProcessorInterface
{
    public function __invoke(GetVideo $dto): void;
}
