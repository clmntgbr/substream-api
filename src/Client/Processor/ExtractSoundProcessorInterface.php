<?php

namespace App\Client\Processor;

use App\Dto\Processor\ExtractSound;

interface ExtractSoundProcessorInterface
{
    public function __invoke(ExtractSound $dto): void;
}
