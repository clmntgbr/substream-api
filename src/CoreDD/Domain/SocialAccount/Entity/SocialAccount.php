<?php

declare(strict_types=1);

namespace App\CoreDD\Domain\SocialAccount\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\CoreDD\Domain\SocialAccount\Repository\SocialAccountRepository;
use App\CoreDD\Domain\Trait\UuidTrait;
use App\CoreDD\Domain\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: SocialAccountRepository::class)]
#[ApiResource]
class SocialAccount
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(type: 'string', unique: true)]
    #[Groups(['social_account:read'])]
    private string $accountId;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['social_account:read'])]
    private string $provider;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['social_account:read'])]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['social_account:read'])]
    private User $user;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public static function create(string $provider, string $accountId, string $email, User $user): self
    {
        $socialAccount = new self();
        $socialAccount->provider = $provider;
        $socialAccount->accountId = $accountId;
        $socialAccount->email = $email;
        $socialAccount->user = $user;

        return $socialAccount;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getAccountId(): string
    {
        return $this->accountId;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
