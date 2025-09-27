<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidTrait;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: JobRepository::class)]
class Job
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, enumType: JobStatusEnum::class)]
    #[Groups(['job:read'])]
    private JobStatusEnum $status = JobStatusEnum::PENDING;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['job:read'])]
    private string $commandClass;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['job:read'])]
    private ?array $commandData = null;

    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups(['job:read'])]
    private ?array $metadata = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read'])]
    private ?string $errorMessage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['job:read'])]
    private ?string $errorTrace = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public static function create(object $message): self
    {
        $job = new self();
        $job->setStatus(JobStatusEnum::RUNNING);
        $job->setCommandClass(get_class($message));
        $job->setCommandData([$message->getIdentifier()]);

        return $job;
    }

    public function getStatus(): JobStatusEnum
    {
        return $this->status;
    }

    public function setStatus(JobStatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    public function setCommandClass(string $commandClass): self
    {
        $this->commandClass = $commandClass;

        return $this;
    }

    public function getCommandData(): ?array
    {
        return $this->commandData;
    }

    public function setCommandData(?array $commandData): self
    {
        $this->commandData = $commandData;

        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;

        return $this;
    }

    public function getErrorTrace(): ?string
    {
        return $this->errorTrace;
    }

    public function setErrorTrace(?string $errorTrace): self
    {
        $this->errorTrace = $errorTrace;

        return $this;
    }
}
