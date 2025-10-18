<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\Controller\User\RegisterController;
use App\Entity\Trait\UuidTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['user:read']],
            uriTemplate: '/me',
        ),
        new Post(
            uriTemplate: '/register',
            controller: RegisterController::class,
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use UuidTrait;
    use TimestampableEntity;

    #[ORM\Column(length: 180)]
    #[Groups(['user:read'])]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['user:read'])]
    private string $firstname;

    #[ORM\Column(type: Types::STRING)]
    #[Groups(['user:read'])]
    private string $lastname;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: SocialAccount::class, mappedBy: 'user')]
    #[Groups(['user:read'])]
    private Collection $socialAccounts;

    #[ORM\Column]
    private ?string $password = null;

    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
        $this->socialAccounts = new ArrayCollection();
    }

    public static function create(string $firstname, string $lastname, string $email, string $plainPassword): self
    {
        $user = new self();
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $email;
        $user->plainPassword = $plainPassword;
        $user->roles = ['ROLE_USER'];

        return $user;
    }

    #[Groups(['user:read'])]
    public function getId(): Uuid
    {
        return $this->id;
    }

    #[Groups(['user:read'])]
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['user:read'])]
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->firstname.' '.$this->lastname;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function setEmail(string $email): static
    {
        if (null !== $this->email && $this->email !== $email) {
            return $this;
        }

        $this->email = $email;

        return $this;
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function setPlainPassword(string $password): static
    {
        $this->plainPassword = $password;

        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getGoogleAccount(): ?SocialAccount
    {
        $result = $this->socialAccounts->filter(fn (SocialAccount $socialAccount) => 'google' === $socialAccount->getProvider())->first();

        return false === $result ? null : $result;
    }

    public function getFacebookAccount(): ?SocialAccount
    {
        $result = $this->socialAccounts->filter(fn (SocialAccount $socialAccount) => 'facebook' === $socialAccount->getProvider())->first();

        return false === $result ? null : $result;
    }

    public function getTwitterAccount(): ?SocialAccount
    {
        $result = $this->socialAccounts->filter(fn (SocialAccount $socialAccount) => 'twitter' === $socialAccount->getProvider())->first();

        return false === $result ? null : $result;
    }
}
