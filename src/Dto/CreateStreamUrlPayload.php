<?php

namespace App\Dto;

use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class CreateStreamUrlPayload
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        #[Assert\Type('string')]
        private string $name,
        #[Assert\NotBlank]
        #[Assert\Url]
        #[Assert\Length(max: 2048)]
        #[Assert\Regex(pattern: '/^https?:\/\/.+$/i', message: 'Invalid URL')]
        private string $url,
        #[SerializedName('thumbnail_file')]
        #[Assert\NotBlank]
        #[Assert\Type('string')]
        #[Assert\Regex(pattern: '/^data:image\/(jpeg|jpg|png|gif|webp);base64,/', message: 'Invalid base64 image format')]
        private string $thumbnailFile,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        #[Assert\Length(max: 36)]
        private Uuid $optionId,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getOptionId(): Uuid
    {
        return $this->optionId;
    }

    public function getThumbnailFile(): string
    {
        return $this->thumbnailFile;
    }
}
