<?php

namespace App\Core\Application\Command;

use App\Core\Domain\ValueObject\FileName;
use App\Core\Domain\ValueObject\OriginalFileName;
use App\Core\Domain\ValueObject\StreamId;
use App\Core\Domain\ValueObject\Url;
use App\Shared\Application\Command\AsyncCommandInterface;
use App\Shared\Application\Command\SyncCommandInterface;
use App\Shared\Application\Middleware\TrackableCommandInterface;

class CreateStreamCommand implements SyncCommandInterface, TrackableCommandInterface
{
    public function __construct(
        public ?StreamId $streamId,
        private ?FileName $streamFileName = null,
        private ?OriginalFileName $streamOriginalFileName = null,
        private ?Url $streamUrl = null,
    ) {
    }

    public function getStreamId(): ?StreamId
    {
        return $this->streamId;
    }

    public function getStreamFileName(): ?FileName
    {
        return $this->streamFileName;
    }

    public function getStreamOriginalFileName(): ?OriginalFileName
    {
        return $this->streamOriginalFileName;
    }
    
    public function getStreamUrl(): ?Url
    {
        return $this->streamUrl;
    }
}
