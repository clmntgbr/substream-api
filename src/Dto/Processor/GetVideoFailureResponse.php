<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class GetVideoFailureResponse
{
    public function __construct(
        #[SerializedName('stream_id')]
        public readonly string $streamId,
    ) {
    }
}