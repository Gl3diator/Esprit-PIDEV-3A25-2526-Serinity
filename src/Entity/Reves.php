<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sommeil;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Reves
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Sommeil::class, inversedBy: "revess")]
    #[ORM\JoinColumn(name: 'sommeil_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    #[Assert\NotNull(message: 'Veuillez associer une nuit de sommeil.')]
    private ?Sommeil $sommeil_id = null;

    #[ORM\Column(type: "string", length: 200)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 200,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $titre = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['😄 Joyeux', '😢 Triste', '😨 Effrayé', '😌 Serein', '😐 Neutre'],
        message: 'Choisissez une humeur valide.'
    )]
    private ?string $humeur = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Le type de rêve est obligatoire.')]
    #[Assert\Choice(
        choices: ['Normal', 'Lucide', 'Cauchemar'],
        message: 'Type de rêve invalide.'
    )]
    private ?string $type_reve = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotNull(message: "L'intensité est obligatoire.")]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: "L'intensité doit être entre {{ min }} et {{ max }}."
    )]
    private ?int $intensite = null;

    #[ORM\Column(type: "boolean")]
    private bool $couleur = false;

    #[ORM\Column(type: "string", length: 200, nullable: true)]
    #[Assert\Length(
        max: 200,
        maxMessage: 'Les émotions ne doivent pas dépasser {{ limit }} caractères.'
    )]
    private ?string $emotions = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $symboles = null;

    #[ORM\Column(type: "boolean")]
    private bool $recurrent = false;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updated_at = null;

    // ─── GETTERS / SETTERS ────────────────────────────────────

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSommeilId(): ?Sommeil
    {
        return $this->sommeil_id;
    }

    public function setSommeilId(?Sommeil $value): void
    {
        $this->sommeil_id = $value;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $value): void
    {
        $this->titre = $value;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $value): void
    {
        $this->description = $value;
    }

    public function getHumeur(): ?string
    {
        return $this->humeur;
    }

    public function setHumeur(?string $value): void
    {
        $this->humeur = $value;
    }

    public function getTypeReve(): ?string
    {
        return $this->type_reve;
    }

    public function setTypeReve(?string $value): void
    {
        $this->type_reve = $value;
    }

    public function getIntensite(): ?int
    {
        return $this->intensite;
    }

    public function setIntensite(?int $value): void
    {
        $this->intensite = $value;
    }

    public function getCouleur(): bool
    {
        return $this->couleur;
    }

    public function setCouleur(bool $value): void
    {
        $this->couleur = $value;
    }

    public function getEmotions(): ?string
    {
        return $this->emotions;
    }

    public function setEmotions(?string $value): void
    {
        $this->emotions = $value;
    }

    public function getSymboles(): ?string
    {
        return $this->symboles;
    }

    public function setSymboles(?string $value): void
    {
        $this->symboles = $value;
    }

    public function getRecurrent(): bool
    {
        return $this->recurrent;
    }

    public function setRecurrent(bool $value): void
    {
        $this->recurrent = $value;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $value): void
    {
        $this->created_at = $value;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $value): void
    {
        $this->updated_at = $value;
    }
}