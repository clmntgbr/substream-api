<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Controller\Stream\CreateStreamUrlController;
use App\Controller\Stream\CreateStreamVideoController;
use App\Controller\Stream\BuildArchiveStreamController;
use App\Entity\Trait\UuidTrait;
use App\Enum\StreamStatusEnum;
use App\Filter\DeletedFilter;
use App\Filter\IncludeDeletedStreamFilter;
use App\Repository\StreamRepository;
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
    order: ['createdAt' => 'DESC'],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
        ),
        new Get(
            uriTemplate: '/streams/{id}/download',
            controller: BuildArchiveStreamController::class,
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['stream:read', 'option:read']],
            parameters: [
                'status' => new QueryParameter(description: 'Filter streams by status'),
                'include_deleted' => new QueryParameter(filter: new IncludeDeletedStreamFilter()),
            ]
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
#[ApiFilter(SearchFilter::class, properties: ['status'])]
class Stream
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $originalFileName = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Groups(['stream:read'])]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Groups(['stream:read'])]
    private ?int $size = null;

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

    #[ORM\Column(type: Types::JSON, nullable: true)]
    private ?array $chunkFileNames = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['stream:read'])]
    private string $status;

    #[ORM\Column(type: Types::JSON)]
    private array $statuses = [];

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Option::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['stream:read'])]
    private Option $option;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'stream')]
    #[ORM\OrderBy(["createdAt" => "ASC"])]
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
    ): self {
        $stream = new self();
        $stream->id = $id;
        $stream->option = $option;
        $stream->fileName = $fileName;
        $stream->originalFileName = $originalFileName;
        $stream->url = $url;
        $stream->mimeType = $mimeType;
        $stream->size = $size;
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
        return in_array($this->status, [
            StreamStatusEnum::UPLOADED->value,
            StreamStatusEnum::CREATED->value,
            StreamStatusEnum::UPLOADING->value,
            StreamStatusEnum::EXTRACTING_SOUND->value,
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED->value,
            StreamStatusEnum::GENERATING_SUBTITLE->value,
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED->value,
            StreamStatusEnum::RESIZING_VIDEO->value,
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED->value,
            StreamStatusEnum::EMBEDDING_VIDEO->value,
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED->value,
            StreamStatusEnum::CHUNKING_VIDEO->value,
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED->value,
        ]);
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isCompleted')]
    public function isCompleted(): bool
    {
        return $this->status === StreamStatusEnum::COMPLETED->value;
    }

    #[Groups(['stream:read'])]
    #[SerializedName('isFailed')]
    public function isFailed(): bool
    {
        return in_array($this->status, [
            StreamStatusEnum::FAILED->value,
            StreamStatusEnum::UPLOAD_FAILED->value,
            StreamStatusEnum::EXTRACTING_SOUND_FAILED->value,
            StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED->value,
            StreamStatusEnum::RESIZING_VIDEO_FAILED->value,
            StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value,
            StreamStatusEnum::CHUNKING_VIDEO_FAILED->value,
            StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value,
        ]);
    }

    #[Groups(['stream:read'])]
    public function getSizeInMegabytes(): int
    {
        return (int) ($this->size / 1024 / 1024);
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

    public function markAsUploadFailed(): self
    {
        $this->status = StreamStatusEnum::UPLOAD_FAILED->value;
        $this->statuses[] = StreamStatusEnum::UPLOAD_FAILED->value;

        return $this;
    }

    public function markAsExtractSoundFailed(): self
    {
        $this->status = StreamStatusEnum::EXTRACTING_SOUND_FAILED->value;
        $this->statuses[] = StreamStatusEnum::EXTRACTING_SOUND_FAILED->value;

        return $this;
    }

    public function markAsGenerateSubtitleFailed(): self
    {
        $this->status = StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value;
        $this->statuses[] = StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value;

        return $this;
    }

    public function markAsTransformingSubtitleFailed(): self
    {
        $this->status = StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED->value;
        $this->statuses[] = StreamStatusEnum::TRANSFORMING_SUBTITLE_FAILED->value;

        return $this;
    }

    public function markAsResizingVideoFailed(): self
    {
        $this->status = StreamStatusEnum::RESIZING_VIDEO_FAILED->value;
        $this->statuses[] = StreamStatusEnum::RESIZING_VIDEO_FAILED->value;

        return $this;
    }

    public function markAsGeneratingSubtitleFailed(): self
    {
        $this->status = StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value;
        $this->statuses[] = StreamStatusEnum::GENERATING_SUBTITLE_FAILED->value;

        return $this;
    }

    public function markAsChunkingVideoFailed(): self
    {
        $this->status = StreamStatusEnum::CHUNKING_VIDEO_FAILED->value;
        $this->statuses[] = StreamStatusEnum::CHUNKING_VIDEO_FAILED->value;

        return $this;
    }

    public function markAsEmbeddingVideoFailed(): self
    {
        $this->status = StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value;
        $this->statuses[] = StreamStatusEnum::EMBEDDING_VIDEO_FAILED->value;

        return $this;
    }

    public function markAsDeleted(): self
    {
        $this->status = StreamStatusEnum::DELETED->value;
        $this->statuses[] = StreamStatusEnum::DELETED->value;

        return $this;
    }

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

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

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

    public function setChunkFileNames(array $chunkFileNames): self
    {
        $this->chunkFileNames = $chunkFileNames;

        return $this;
    }

    /**
     * @return list<string>
     */
    public function getChunkFileNames(): ?array
    {
        return $this->chunkFileNames;
    }

    #[Groups(['stream:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['stream:read'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

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
        $processingTime = $this->getProcessingTimeInMilliseconds();
        $totalSeconds = (int) floor($processingTime / 1000);
        $hours = (int) floor($totalSeconds / 3600);
        $minutes = (int) floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        if (0 === $hours && 0 === $minutes && 0 === $seconds) {
            return null;
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
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
        $statusEnum = StreamStatusEnum::from($this->status);
        
        if (str_contains($this->status, 'failed')) {
            return 100;
        }
        
        return match ($statusEnum) {
            StreamStatusEnum::CREATED => 0,
            StreamStatusEnum::UPLOADING => 5,
            StreamStatusEnum::UPLOADED => 10,
            StreamStatusEnum::EXTRACTING_SOUND => 20,
            StreamStatusEnum::EXTRACTING_SOUND_COMPLETED => 30,
            StreamStatusEnum::GENERATING_SUBTITLE => 40,
            StreamStatusEnum::GENERATING_SUBTITLE_COMPLETED => 50,
            StreamStatusEnum::TRANSFORMING_SUBTITLE => 60,
            StreamStatusEnum::TRANSFORMING_SUBTITLE_COMPLETED => 70,
            StreamStatusEnum::RESIZING_VIDEO => 75,
            StreamStatusEnum::RESIZING_VIDEO_COMPLETED => 80,
            StreamStatusEnum::EMBEDDING_VIDEO => 85,
            StreamStatusEnum::EMBEDDING_VIDEO_COMPLETED => 90,
            StreamStatusEnum::CHUNKING_VIDEO => 95,
            StreamStatusEnum::CHUNKING_VIDEO_COMPLETED, 
            StreamStatusEnum::COMPLETED,
            StreamStatusEnum::DELETED => 100,
            default => 0,
        };
    }

    public function isDownloadable(): bool
    {
        return in_array($this->status, [
            StreamStatusEnum::COMPLETED->value,
        ]);
    }

    public function getCleanableFiles(): array
    {
        $audioFiles = [];
        foreach ($this->getAudioFiles() as $audioFile) {
            $audioFiles[] = 'audios/'.$audioFile;
        }
        
        return [
            ...$audioFiles,
            'subtitles/'.$this->getSubtitleSrtFileName(),
            'subtitles/'.$this->getSubtitleAssFileName(),
            $this->getResizeFileName(),
            $this->getEmbedFileName(),
        ];
    }
}
