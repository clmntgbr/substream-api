<?php

declare(strict_types=1);

namespace App\Domain\Core\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ChunkVideoFailure
{
    public function __construct(
        #[SerializedName('stream_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $streamId,
        #[SerializedName('task_id')]
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private readonly Uuid $taskId,
    ) {
    }

    public function getStreamId(): Uuid
    {
        return $this->streamId;
    }

    public function getTaskId(): Uuid
    {
        return $this->taskId;
    }
}
