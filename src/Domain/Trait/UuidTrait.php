<?php

declare(strict_types=1);

namespace App\Domain\Trait;

use ApiPlatform\Metadata\ApiProperty;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidTrait
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ApiProperty(identifier: true)]
    private Uuid $id;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setId(Uuid $uuid): self
    {
        $this->id = $uuid;

        return $this;
    }

    public function setIdFromString(string $uuid): self
    {
        $this->id = Uuid::fromString($uuid);

        return $this;
    }
}
