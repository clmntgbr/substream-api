<?php

namespace App\Dto\Webhook;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ChunkVideoSuccess
{
    public function __construct(
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $streamId,
        #[SerializedName('chunk_file_names')]
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(max: 255),
        ])]
        private readonly array $chunkFileNames,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getChunkFileNames(): array
    {
        return $this->chunkFileNames;
    }
}
