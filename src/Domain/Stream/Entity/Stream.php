<?php

declare(strict_types=1);

namespace App\Domain\Stream\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Domain\Option\Entity\Option;
use App\Domain\Stream\Enum\StreamStatusEnum;
use App\Domain\Stream\Repository\StreamRepository;
use App\Domain\Stream\ValueObject\StreamStatus;
use App\Domain\Task\Entity\Task;
use App\Domain\Trait\UuidTrait;
use App\Domain\User\Entity\User;
use App\Infrastructure\Stream\Filter\StatusSearchFilter;
use App\Presentation\Controller\Stream\CreateStreamUrlController;
use App\Presentation\Controller\Stream\CreateStreamVideoController;
use App\Presentation\Controller\Stream\DeleteStreamController;
use App\Presentation\Controller\Stream\Download\GetArchiveStreamController;
use App\Presentation\Controller\Stream\Download\GetResumeStreamController;
use App\Presentation\Controller\Stream\Download\GetSubtitleStreamController;
use App\Shared\Utils\DurationFormatter;
use App\Shared\Utils\StreamFileCleaner;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: StreamRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC', 'size' => 'DESC', 'originalFileName' => 'DESC'],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
            parameters: [
                'originalFileName' => new QueryParameter(
                    description: 'Search streams by original file name',
                    property: 'originalFileName',
                    filter: new PartialSearchFilter(),
                ),
                'createdAt' => new QueryParameter(
                    description: 'Search streams by created at',
                    property: 'createdAt',
                    filter: new DateFilter(),
                ),
                'status' => new QueryParameter(
                    description: 'Search streams by status',
                    property: 'status',
                    filter: new StatusSearchFilter(),
                ),
            ],
        ),
        new Get(
            uriTemplate: '/streams/{id}/delete',
            controller: DeleteStreamController::class,
            normalizationContext: ['groups' => ['stream:read']],
        ),
        new Get(
            uriTemplate: '/streams/{id}/download',
            controller: GetArchiveStreamController::class,
        ),
        new Get(
            uriTemplate: '/streams/{id}/download/subtitle',
            controller: GetSubtitleStreamController::class,
        ),
        new Get(
            uriTemplate: '/streams/{id}/download/resume',
            controller: GetResumeStreamController::class,
        ),
        new Post(
            uriTemplate: '/streams/video',
            controller: CreateStreamVideoController::class,
        ),
        new Post(
            uriTemplate: '/streams/url',
            controller: CreateStreamUrlController::class,
        ),
    ]
)]
class Stream
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read', 'stream:search'])]
    private ?string $originalFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $thumbnailUrl = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['stream:read'])]
    private ?int $size = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['stream:read'])]
    private ?int $duration = null;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private array $audioFiles = [];

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $subtitleSrtFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $subtitleAssFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $resizeFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $embedFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $resumeFileName = null;

    /**
     * @var array<int, string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $chunkFileNames = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $status;

    /**
     * @var array<int, string>
     */
    #[ORM\Column(type: Types::JSON)]
    #[Groups(['stream:read'])]
    private array $statuses = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Option::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['option:read'])]
    private Option $option;

    /**
     * @var Collection<int, Task>
     */
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'stream')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public static function create(
        Uuid $id,
        User $user,
        Option $option,
        ?string $fileName = null,
        ?string $originalFileName = null,
        ?string $url = null,
        ?string $mimeType = null,
        ?int $size = null,
        ?int $duration = null,
    ): self {
        $stream = new self();
        $stream->id = $id;
        $stream->option = $option;
        $stream->fileName = $fileName;
        $stream->originalFileName = $originalFileName;
        $stream->url = $url;
        $stream->mimeType = $mimeType ?? 'video/mp4';
        $stream->size = $size;
        $stream->duration = $duration;
        $stream->status = StreamStatusEnum::CREATED->value;
        $stream->statuses = [StreamStatusEnum::CREATED->value];
        $stream->user = $user;

        return $stream;
    }

    #[Groups(['stream:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isProcessing')]
    public function isProcessing(): bool
    {
        return $this->getStatusVO()->isProcessing();
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isCompleted')]
    public function isCompleted(): bool
    {
        return $this->getStatusVO()->isCompleted();
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isFailed')]
    public function isFailed(): bool
    {
        return $this->getStatusVO()->isFailed();
    }

    #[Groups(['stream:read'])]
    public function getSizeInMegabytes(): ?int
    {
        return DurationFormatter::bytesToMegabytes($this->size);
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getOption(): Option
    {
        return $this->option;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function getOriginalFileName(): ?string
    {
        return $this->originalFileName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array<int, string>
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setStatus(string $status): self
    {
        $this->status = StreamStatusEnum::from($status)->value;
        $this->statuses[] = StreamStatusEnum::from($status)->value;

        return $this;
    }

    /**
     * @param array<int, string> $statuses
     */
    public function setStatuses(array $statuses): self
    {
        $this->statuses = $statuses;

        return $this;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function markAsFailed(StreamStatusEnum $status): self
    {
        $this->status = $status->value;
        $this->statuses[] = $status->value;

        return $this;
    }

    public function markAsDeleted(): self
    {
        $this->status = StreamStatusEnum::DELETED->value;
        $this->statuses[] = StreamStatusEnum::DELETED->value;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public function getAudioFiles(): array
    {
        return $this->audioFiles;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setOriginalFileName(string $originalFileName): self
    {
        $this->originalFileName = $originalFileName;

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

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param array<int, string> $audioFiles
     */
    public function setAudioFiles(array $audioFiles): self
    {
        $this->audioFiles = $audioFiles;

        return $this;
    }

    public function setSubtitleSrtFileName(string $subtitleSrtFileName): self
    {
        $this->subtitleSrtFileName = $subtitleSrtFileName;

        return $this;
    }

    public function getSubtitleSrtFileName(): ?string
    {
        return $this->subtitleSrtFileName;
    }

    public function setSubtitleAssFileName(?string $subtitleAssFileName): self
    {
        $this->subtitleAssFileName = $subtitleAssFileName;

        return $this;
    }

    public function getSubtitleAssFileName(): ?string
    {
        return $this->subtitleAssFileName;
    }

    public function setResizeFileName(?string $resizeFileName): self
    {
        $this->resizeFileName = $resizeFileName;

        return $this;
    }

    public function getResizeFileName(): ?string
    {
        return $this->resizeFileName;
    }

    public function setEmbedFileName(?string $embedFileName): self
    {
        $this->embedFileName = $embedFileName;

        return $this;
    }

    public function getEmbedFileName(): ?string
    {
        return $this->embedFileName;
    }

    /**
     * @param array<int, string> $chunkFileNames
     */
    public function setChunkFileNames(array $chunkFileNames): self
    {
        $this->chunkFileNames = $chunkFileNames;

        return $this;
    }

    public function setResumeFileName(?string $resumeFileName): self
    {
        $this->resumeFileName = $resumeFileName;

        return $this;
    }

    public function getResumeFileName(): ?string
    {
        return $this->resumeFileName;
    }

    /**
     * @return list<string>|null
     */
    public function getChunkFileNames(): ?array
    {
        if (null === $this->chunkFileNames) {
            return null;
        }

        // Ensure we return a list (sequential array with numeric keys starting at 0)
        return array_values($this->chunkFileNames);
    }

    #[Groups(['stream:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        if (null === $this->createdAt) {
            throw new \RuntimeException('CreatedAt is not set');
        }

        return $this->createdAt;
    }

    #[Groups(['stream:read'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        if (null === $this->updatedAt) {
            throw new \RuntimeException('UpdatedAt is not set');
        }

        return $this->updatedAt;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Collection<int, Task> $tasks
     */
    public function setTasks(Collection $tasks): self
    {
        $this->tasks = $tasks;

        return $this;
    }

    public function getProcessingTimeInMilliseconds(): int
    {
        return $this->tasks
            ->map(fn (Task $task) => $task->getProcessingTime())
            ->reduce(fn (int $carry, ?int $item) => $carry + $item, 0);
    }

    public function getProcessingTimeInSeconds(): int
    {
        return $this->getProcessingTimeInMilliseconds() / 1000;
    }

    #[Groups(['stream:read'])]
    #[SerializedName('processingTime')]
    public function getProcessingTimeFormatted(): ?string
    {
        return DurationFormatter::format($this->getProcessingTimeInMilliseconds());
    }

    public function setOption(Option $option): self
    {
        $this->option = $option;

        return $this;
    }

    #[Groups(['stream:read'])]
    #[SerializedName('progress')]
    public function getProgress(): int
    {
        return $this->getStatusVO()->getProgress();
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isDownloadable')]
    public function isDownloadable(): bool
    {
        return $this->getStatusVO()->isDownloadable();
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isSrtDownloadable')]
    public function isSrtDownloadable(): bool
    {
        return $this->getStatusVO()->isSrtDownloadable($this->getSubtitleSrtFileName());
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isResumeDownloadable')]
    public function isResumeDownloadable(): bool
    {
        return $this->getStatusVO()->isResumeDownloadable($this->getResumeFileName());
    }

    /**
     * @return array<int, string>
     */
    public function getCleanableFiles(): array
    {
        return (new StreamFileCleaner())->getCleanableFiles(
            $this->getAudioFiles(),
            $this->getSubtitleAssFileName(),
            $this->getResizeFileName(),
            $this->getEmbedFileName()
        );
    }

    #[Groups(['stream:read'])]
    #[SerializedName('duration')]
    public function getDuration(): ?string
    {
        return DurationFormatter::formatFromSeconds($this->duration);
    }

    public function getThumbnailUrl(): ?string
    {
        return $this->thumbnailUrl;
    }

    public function setThumbnailUrl(?string $thumbnailUrl): self
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    #[Groups(['stream:search'])]
    public function getFilterStatus(): ?string
    {
        return $this->getStatusVO()->getFilterStatus();
    }

    #[Groups(['stream:search', 'elastica'])]
    public function getUserUuid(): string
    {
        return (string) $this->user->getId();
    }

    private function getStatusVO(): StreamStatus
    {
        return new StreamStatus($this->status, $this->statuses);
    }
}
