<?php

namespace App\Dto\Processor;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

final class ExtractSoundProcessorSuccess
{
    public function __construct(
        #[SerializedName('audio_files')]
        #[Assert\NotBlank]
        #[Assert\All([
            new Assert\NotBlank(),
            new Assert\Type('string'),
            new Assert\Length(max: 255),
        ])]
        public readonly array $audioFiles,
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        public readonly string $streamId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return Uuid::fromString($this->streamId);
    }

    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }
}
