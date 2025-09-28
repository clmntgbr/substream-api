<?php

namespace App\Client\Processor;

use App\Dto\ExtractSound;

interface ExtractSoundProcessorInterface
{
    public function __invoke(ExtractSound $dto): void;
}
