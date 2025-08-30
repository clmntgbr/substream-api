<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\Entity\Trait\UuidTrait;
use App\Repository\StreamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StreamRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['skip_null_values' => false, 'groups' => ['stream:read']],
        ),
    ]
)]
class Stream
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $fileName;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $mimeType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['stream:read'])]
    private int $size;

    public function __construct()
    {
    }

    public function create(Uuid $uuid, string $fileName, string $mimeType, int $size): self
    {
        $this->id = $uuid;
        $this->fileName = $fileName;
        $this->mimeType = $mimeType;
        $this->size = $size;

        return $this;
    }

    #[Groups(['stream:read'])]
    public function getId(): string
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
    
    public function getMimeType(): string
    {
        return $this->mimeType;
    }
    
    public function getSize(): int
    {
        return $this->size;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }
    
    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }
    
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }
}
