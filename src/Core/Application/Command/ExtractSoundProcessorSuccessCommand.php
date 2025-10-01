<?php

namespace App\Core\Application\Command;

use App\Core\Application\Trait\CommandIdTrait;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;
use Symfony\Component\Uid\Uuid;

class ExtractSoundProcessorSuccessCommand implements AsyncCommandInterface
{
    public function __construct(
        public Uuid $streamId,
        public readonly array $audioFiles,
    ) {
    }
}
