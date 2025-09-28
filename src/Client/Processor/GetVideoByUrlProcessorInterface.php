<?php

namespace App\Client\Processor;

use App\Dto\GetVideoByUrl;

interface GetVideoByUrlProcessorInterface
{
    public function __invoke(GetVideoByUrl $dto): void;
}
