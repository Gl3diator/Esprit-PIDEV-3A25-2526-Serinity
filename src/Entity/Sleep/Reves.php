<?php

namespace App\Entity\Sleep;

use App\Repository\Sleep\RevesRepository;
use App\Entity\Sleep\Sommeil;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RevesRepository::class)]
#[ORM\Table(name: 'reves')]
class Reves
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    // ✅ Fix L.22 : nullable: true dans JoinColumn pour matcher ?Sommeil
    #[ORM\ManyToOne(targetEntity: Sommeil::class, inversedBy: "reves")]
    #[ORM\JoinColumn(name: 'sommeil_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: true)]
    #[Assert\NotNull(message: 'Veuillez associer une nuit de sommeil.')]
    private ?Sommeil $sommeil = null;

    // ✅ Fix L.32 : nullable: true pour matcher ?string
    #[ORM\Column(type: "string", length: 200, nullable: true)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, max: 200,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $titre = null;

    // ✅ Fix L.40 : nullable: true pour matcher ?string
    #[ORM\Column(type: "text", nullable: true)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['😄 Joyeux', '😢 Triste', '😨 Effrayé', '😌 Serein', '😐 Neutre'],
        message: 'Choisissez une humeur valide.'
    )]
    private ?string $humeur = null;

    #[ORM\Column(name: 'type_reve', type: "string", length: 50, nullable: true)]
    #[Assert\NotBlank(message: 'Le type de rêve est obligatoire.')]
    #[Assert\Choice(
        choices: ['Normal', 'Lucide', 'Cauchemar'],
        message: 'Type de rêve invalide.'
    )]
    private ?string $typeReve = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\NotNull(message: "L'intensité est obligatoire.")]
    #[Assert\Range(min: 1, max: 10, notInRangeMessage: "L'intensité doit être entre {{ min }} et {{ max }}.")]
    private ?int $intensite = null;

    #[ORM\Column(type: "boolean", options: ['default' => true])]
    private bool $couleur = true;

    #[ORM\Column(type: "string", length: 200, nullable: true)]
    #[Assert\Length(max: 200, maxMessage: 'Les émotions ne doivent pas dépasser {{ limit }} caractères.')]
    private ?string $emotions = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'Les symboles ne doivent pas dépasser {{ limit }} caractères.')]
    private ?string $symboles = null;

    #[ORM\Column(type: "boolean", options: ['default' => false])]
    private bool $recurrent = false;

    // ✅ Fix L.87 : non-nullable car initialisé dans __construct
    #[ORM\Column(name: 'created_at', type: "datetime", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $createdAt;

    // ✅ Fix L.90 : non-nullable car initialisé dans __construct
    #[ORM\Column(name: 'updated_at', type: "datetime", options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getSommeil(): ?Sommeil { return $this->sommeil; }
    public function setSommeil(?Sommeil $sommeil): static { $this->sommeil = $sommeil; return $this; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $value): static { $this->titre = $value; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $value): static { $this->description = $value; return $this; }

    public function getHumeur(): ?string { return $this->humeur; }
    public function setHumeur(?string $value): static { $this->humeur = $value; return $this; }

    public function getTypeReve(): ?string { return $this->typeReve; }
    public function setTypeReve(?string $value): static { $this->typeReve = $value; return $this; }

    public function getIntensite(): ?int { return $this->intensite; }
    public function setIntensite(?int $value): static { $this->intensite = $value; return $this; }

    public function isCouleur(): bool { return $this->couleur; }
    public function setCouleur(bool $value): static { $this->couleur = $value; return $this; }

    public function getEmotions(): ?string { return $this->emotions; }
    public function setEmotions(?string $value): static { $this->emotions = $value; return $this; }

    public function getSymboles(): ?string { return $this->symboles; }
    public function setSymboles(?string $value): static { $this->symboles = $value; return $this; }

    public function isRecurrent(): bool { return $this->recurrent; }
    public function setRecurrent(bool $value): static { $this->recurrent = $value; return $this; }

    // ✅ getters non-nullable
    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $value): static { $this->createdAt = $value; return $this; }

    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $value): static { $this->updatedAt = $value; return $this; }
}