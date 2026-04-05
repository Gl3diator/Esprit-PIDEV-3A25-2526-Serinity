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
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "date")]
    #[Assert\NotBlank(message: 'La date de nuit est obligatoire.')]
    private \DateTimeInterface $date_nuit;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "L'heure de coucher est obligatoire.")]
    private string $heure_coucher;

    #[ORM\Column(type: "string")]
    #[Assert\NotBlank(message: "L'heure de réveil est obligatoire.")]
    private string $heure_reveil;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\NotBlank(message: 'La qualité est obligatoire.')]
    #[Assert\Choice(
        choices: ['Excellente', 'Bonne', 'Moyenne', 'Mauvaise'],
        message: 'Choisissez une qualité valide.'
    )]
    private string $qualite;

    #[ORM\Column(type: "text")]
    #[Assert\Length(max: 1000, maxMessage: 'Le commentaire ne doit pas dépasser {{ limit }} caractères.')]
    private string $commentaire;

    #[ORM\Column(type: "float")]
    #[Assert\Positive(message: 'La durée doit être un nombre positif.')]
    private float $duree_sommeil;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero(message: 'Les interruptions ne peuvent pas être négatives.')]
    private int $interruptions;

    #[ORM\Column(type: "string", length: 50)]
    private string $humeur_reveil;

    #[ORM\Column(type: "string", length: 100)]
    private string $environnement;

    #[ORM\Column(type: "float")]
    #[Assert\Range(
        min: 10,
        max: 40,
        notInRangeMessage: 'La température doit être entre {{ min }}°C et {{ max }}°C.'
    )]
    private float $temperature;

    #[ORM\Column(type: "string", length: 50)]
    private string $bruit_niveau;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $updated_at;

    #[ORM\Column(type: "integer")]
    private int $user_id;

    #[ORM\OneToMany(mappedBy: "sommeil_id", targetEntity: Reves::class)]
    private Collection $revess;

    public function __construct()
    {
        $this->revess = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getDate_nuit()
    {
        return $this->date_nuit;
    }

    public function setDate_nuit($value)
    {
        $this->date_nuit = $value;
    }

    public function getHeure_coucher()
    {
        return $this->heure_coucher;
    }

    public function setHeure_coucher($value)
    {
        $this->heure_coucher = $value;
    }

    public function getHeure_reveil()
    {
        return $this->heure_reveil;
    }

    public function setHeure_reveil($value)
    {
        $this->heure_reveil = $value;
    }

    public function getQualite()
    {
        return $this->qualite;
    }

    public function setQualite($value)
    {
        $this->qualite = $value;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($value)
    {
        $this->commentaire = $value;
    }

    public function getDuree_sommeil()
    {
        return $this->duree_sommeil;
    }

    public function setDuree_sommeil($value)
    {
        $this->duree_sommeil = $value;
    }

    public function getInterruptions()
    {
        return $this->interruptions;
    }

    public function setInterruptions($value)
    {
        $this->interruptions = $value;
    }

    public function getHumeur_reveil()
    {
        return $this->humeur_reveil;
    }

    public function setHumeur_reveil($value)
    {
        $this->humeur_reveil = $value;
    }

    public function getEnvironnement()
    {
        return $this->environnement;
    }

    public function setEnvironnement($value)
    {
        $this->environnement = $value;
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function setTemperature($value)
    {
        $this->temperature = $value;
    }

    public function getBruit_niveau()
    {
        return $this->bruit_niveau;
    }

    public function setBruit_niveau($value)
    {
        $this->bruit_niveau = $value;
    }

    public function getCreated_at()
    {
        return $this->created_at;
    }

    public function setCreated_at($value)
    {
        $this->created_at = $value;
    }

    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    public function setUpdated_at($value)
    {
        $this->updated_at = $value;
    }

    public function getUser_id()
    {
        return $this->user_id;
    }

    public function setUser_id($value)
    {
        $this->user_id = $value;
    }

    public function getRevess(): Collection
    {
        return $this->revess;
    }

    public function addReves(Reves $reves): self
    {
        if (!$this->revess->contains($reves)) {
            $this->revess[] = $reves;
            $reves->setSommeil_id($this);
        }
        return $this;
    }

    public function removeReves(Reves $reves): self
    {
        if ($this->revess->removeElement($reves)) {
            if ($reves->getSommeil_id() === $this) {
                $reves->setSommeil_id(null);
            }
        }
        return $this;
    }
}