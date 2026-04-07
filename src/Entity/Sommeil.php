<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Reves;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Sommeil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: 'La date de nuit est obligatoire.')]
    #[Assert\LessThanOrEqual(
        value: 'today',
        message: 'La date de nuit ne peut pas être dans le futur.'
    )]
    private ?\DateTimeInterface $date_nuit = null;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "L'heure de coucher est obligatoire.")]
    private ?string $heure_coucher = null;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "L'heure de réveil est obligatoire.")]
    private ?string $heure_reveil = null;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: 'La qualité est obligatoire.')]
    #[Assert\Choice(
        choices: ['Excellente', 'Bonne', 'Moyenne', 'Mauvaise'],
        message: 'Choisissez une qualité valide.'
    )]
    private ?string $qualite = null;

    #[ORM\Column(type: "text", nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'Le commentaire ne doit pas dépasser {{ limit }} caractères.'
    )]
    private ?string $commentaire = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\Positive(message: 'La durée doit être un nombre positif.')]
    #[Assert\Range(
        min: 0.5,
        max: 24,
        notInRangeMessage: 'La durée du sommeil doit être entre {{ min }} et {{ max }} heures.'
    )]
    private ?float $duree_sommeil = null;

    #[ORM\Column(type: "integer", nullable: true)]
    #[Assert\PositiveOrZero(message: 'Les interruptions ne peuvent pas être négatives.')]
    private ?int $interruptions = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['😌 Reposé', '😄 Joyeux', '😐 Neutre', '😴 Fatigué', '⚡ Énergisé'],
        message: 'Choisissez une humeur valide.'
    )]
    private ?string $humeur_reveil = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    #[Assert\Choice(
        choices: ['🏠 Normal', '🌿 Calme', '😊 Confortable'],
        message: 'Choisissez un environnement valide.'
    )]
    private ?string $environnement = null;

    #[ORM\Column(type: "float", nullable: true)]
    #[Assert\Range(
        min: 10,
        max: 40,
        notInRangeMessage: 'La température doit être entre {{ min }}°C et {{ max }}°C.'
    )]
    private ?float $temperature = null;

    #[ORM\Column(type: "string", length: 50, nullable: true)]
    #[Assert\Choice(
        choices: ['🔇 Silencieux', '🔉 Léger', '🔉 Modéré', '🔊 Fort'],
        message: 'Choisissez un niveau de bruit valide.'
    )]
    private ?string $bruit_niveau = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $user_id = null;

    #[ORM\OneToMany(mappedBy: "sommeil_id", targetEntity: Reves::class)]
    private Collection $revess;

    public function __construct()
    {
        $this->revess = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateNuit(): ?\DateTimeInterface
    {
        return $this->date_nuit;
    }

    public function setDateNuit(?\DateTimeInterface $value): self
    {
        $this->date_nuit = $value;
        return $this;
    }

    public function getHeureCoucher(): ?string
    {
        return $this->heure_coucher;
    }

    public function setHeureCoucher(?string $value): self
    {
        $this->heure_coucher = $value;
        return $this;
    }

    public function getHeureReveil(): ?string
    {
        return $this->heure_reveil;
    }

    public function setHeureReveil(?string $value): self
    {
        $this->heure_reveil = $value;
        return $this;
    }

    public function getQualite(): ?string
    {
        return $this->qualite;
    }

    public function setQualite(?string $value): self
    {
        $this->qualite = $value;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $value): self
    {
        $this->commentaire = $value;
        return $this;
    }

    public function getDureeSommeil(): ?float
    {
        return $this->duree_sommeil;
    }

    public function setDureeSommeil(?float $value): self
    {
        $this->duree_sommeil = $value;
        return $this;
    }

    public function getInterruptions(): ?int
    {
        return $this->interruptions;
    }

    public function setInterruptions(?int $value): self
    {
        $this->interruptions = $value;
        return $this;
    }

    public function getHumeurReveil(): ?string
    {
        return $this->humeur_reveil;
    }

    public function setHumeurReveil(?string $value): self
    {
        $this->humeur_reveil = $value;
        return $this;
    }

    public function getEnvironnement(): ?string
    {
        return $this->environnement;
    }

    public function setEnvironnement(?string $value): self
    {
        $this->environnement = $value;
        return $this;
    }

    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $value): self
    {
        $this->temperature = $value;
        return $this;
    }

    public function getBruitNiveau(): ?string
    {
        return $this->bruit_niveau;
    }

    public function setBruitNiveau(?string $value): self
    {
        $this->bruit_niveau = $value;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $value): self
    {
        $this->created_at = $value;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $value): self
    {
        $this->updated_at = $value;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $value): self
    {
        $this->user_id = $value;
        return $this;
    }

    public function getRevess(): Collection
    {
        return $this->revess;
    }

    public function addReves(Reves $reves): self
    {
        if (!$this->revess->contains($reves)) {
            $this->revess[] = $reves;
            $reves->setSommeilId($this);
        }

        return $this;
    }

    public function removeReves(Reves $reves): self
    {
        if ($this->revess->removeElement($reves)) {
            if ($reves->getSommeilId() === $this) {
                $reves->setSommeilId(null);
            }
        }

        return $this;
    }

    public function isSommeilInsuffisant(): bool
    {
        return $this->duree_sommeil !== null && $this->duree_sommeil < 5;
    }

    public function getSleepStatusLabel(): ?string
    {
        return $this->isSommeilInsuffisant() ? 'Sommeil insuffisant' : null;
    }
}