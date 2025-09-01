<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class GetVideoResponse
{
    public function __construct(
        #[SerializedName('file_name')]
        public readonly string $fileName,
        #[SerializedName('original_name')]
        public readonly string $originalName,
        #[SerializedName('mime_type')]
        public readonly string $mimeType,
        #[SerializedName('size')]
        public readonly int $size,
        #[SerializedName('stream_id')]
        public readonly string $streamId,
    ) {
    }
}