<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Sommeil;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Reves
{
    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Sommeil::class, inversedBy: "revess")]
    #[ORM\JoinColumn(name: 'sommeil_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\NotNull(message: 'Veuillez associer une nuit de sommeil.')]
    private Sommeil $sommeil_id;

    #[ORM\Column(type: "string", length: 200)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 200,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne doit pas dépasser {{ limit }} caractères.'
    )]
    private string $titre;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit contenir au moins {{ limit }} caractères.'
    )]
    private string $description;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Choice(
        choices: ['😄 Joyeux', '😢 Triste', '😨 Effrayé', '😌 Serein', '😐 Neutre'],
        message: 'Choisissez une humeur valide.'
    )]
    private string $humeur;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Choice(
        choices: ['Normal', 'Lucide', 'Cauchemar', 'Prémonitoire'],
        message: 'Type de rêve invalide.'
    )]
    private string $type_reve;

    #[ORM\Column(type: "integer")]
    #[Assert\Range(
        min: 1,
        max: 10,
        notInRangeMessage: "L'intensité doit être entre {{ min }} et {{ max }}."
    )]
    private int $intensite;

    #[ORM\Column(type: "boolean")]
    private bool $couleur;

    #[ORM\Column(type: "string", length: 200)]
    #[Assert\Length(
        max: 200,
        maxMessage: 'Les émotions ne doivent pas dépasser {{ limit }} caractères.'
    )]
    private string $emotions;

    #[ORM\Column(type: "text")]
    private string $symboles;

    #[ORM\Column(type: "boolean")]
    private bool $recurrent;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $created_at;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $updated_at;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getSommeil_id()
    {
        return $this->sommeil_id;
    }

    public function setSommeil_id($value)
    {
        $this->sommeil_id = $value;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($value)
    {
        $this->titre = $value;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($value)
    {
        $this->description = $value;
    }

    public function getHumeur()
    {
        return $this->humeur;
    }

    public function setHumeur($value)
    {
        $this->humeur = $value;
    }

    public function getType_reve()
    {
        return $this->type_reve;
    }

    public function setType_reve($value)
    {
        $this->type_reve = $value;
    }

    public function getIntensite()
    {
        return $this->intensite;
    }

    public function setIntensite($value)
    {
        $this->intensite = $value;
    }

    public function getCouleur()
    {
        return $this->couleur;
    }

    public function setCouleur($value)
    {
        $this->couleur = $value;
    }

    public function getEmotions()
    {
        return $this->emotions;
    }

    public function setEmotions($value)
    {
        $this->emotions = $value;
    }

    public function getSymboles()
    {
        return $this->symboles;
    }

    public function setSymboles($value)
    {
        $this->symboles = $value;
    }

    public function getRecurrent()
    {
        return $this->recurrent;
    }

    public function setRecurrent($value)
    {
        $this->recurrent = $value;
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
}