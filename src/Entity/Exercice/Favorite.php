<?php

namespace App\Entity\Exercice;

use App\Repository\Exercice\FavoriteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ORM\Table(name: 'favorite')]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire.")]
    private ?User $user = null;

    #[ORM\Column(name: 'favorite_type', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Le type de favori est obligatoire.')]
    #[Assert\Choice(
        choices: ['exercise', 'resource', 'journal'],
        message: 'Type de favori invalide.'
    )]
    private ?string $favoriteType = null;

    #[ORM\Column(name: 'item_id', type: 'integer', nullable: false)]
    #[Assert\NotNull(message: "L'élément est obligatoire.")]
    #[Assert\Positive(message: "L'identifiant de l'élément doit être positif.")]
    private ?int $itemId = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    // ─── Getters / Setters ───────────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getFavoriteType(): ?string
    {
        return $this->favoriteType;
    }

    public function setFavoriteType(string $favoriteType): static
    {
        $this->favoriteType = $favoriteType;
        return $this;
    }

    public function getItemId(): ?int
    {
        return $this->itemId;
    }

    public function setItemId(int $itemId): static
    {
        $this->itemId = $itemId;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}