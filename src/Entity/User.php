<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_UUID', fields: ['uuid'])]
#[UniqueEntity(fields: ['uuid'], message: 'There is already an account with this uuid')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180,unique:true)]
    private ?string $uuid = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 40)]
    private ?string $username = null;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'artist')]
    private Collection $music_released;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotify_username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotify_password = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $spotify_id = null;

    public function __construct()
    {
        $this->music_released = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->uuid;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection<int, Music>
     */
    public function getMusicReleased(): Collection
    {
        return $this->music_released;
    }

    public function addMusicReleased(Music $musicReleased): static
    {
        if (!$this->music_released->contains($musicReleased)) {
            $this->music_released->add($musicReleased);
            $musicReleased->addArtist($this);
        }

        return $this;
    }

    public function removeMusicReleased(Music $musicReleased): static
    {
        if ($this->music_released->removeElement($musicReleased)) {
            $musicReleased->removeArtist($this);
        }

        return $this;
    }

    public function getSpotifyUsername(): ?string
    {
        return $this->spotify_username;
    }

    public function setSpotifyUsername(?string $spotify_username): static
    {
        $this->spotify_username = $spotify_username;

        return $this;
    }

    public function getSpotifyPassword(): ?string
    {
        return $this->spotify_password;
    }

    public function setSpotifyPassword(?string $spotify_password): static
    {
        $this->spotify_password = $spotify_password;

        return $this;
    }

    public function getSpotifyId(): ?string
    {
        return $this->spotify_id;
    }

    public function setSpotifyId(?string $spotify_id): static
    {
        $this->spotify_id = $spotify_id;

        return $this;
    }
}
