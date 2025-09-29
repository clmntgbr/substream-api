<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Trait\UuidTrait;
use App\Enum\JobStatusEnum;
use App\Repository\JobRepository;
use App\Shared\Application\Middleware\TrackableCommandInterface;
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
    private JobStatusEnum $status = JobStatusEnum::RUNNING;

    #[ORM\Column(type: Types::JSON)]
    #[Groups(['job:read'])]
    private array $statuses = [];

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['job:read'])]
    private string $commandClass;

    #[ORM\Column(type: 'uuid')]
    #[Groups(['job:read'])]
    private Uuid $commandId;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public static function create(TrackableCommandInterface $message): self
    {
        $job = new self();
        $job->setStatus(JobStatusEnum::RUNNING);
        $job->setCommandClass(get_class($message));
        $job->setCommandId($message->getCommandId());

        return $job;
    }

    public function getStatus(): JobStatusEnum
    {
        return $this->status;
    }

    public function setStatus(JobStatusEnum $status): self
    {
        $this->statuses[] = $status;
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

    public function getCommandId(): Uuid
    {
        return $this->commandId;
    }

    public function setCommandId(Uuid $commandId): self
    {
        $this->commandId = $commandId;

        return $this;
    }
}
