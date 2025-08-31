<?php

namespace App\Client\Processor;

use App\Dto\Processor\GetVideoByUrl;

interface GetVideoByUrlProcessorInterface
{
    public function __invoke(GetVideoByUrl $dto): void;
}