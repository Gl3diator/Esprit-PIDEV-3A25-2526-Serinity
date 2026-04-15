<?php

namespace App\Entity\Exercice;

use App\Repository\Exercice\ResourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ResourceRepository::class)]
#[ORM\Table(name: 'resource')]
class Resource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le titre de la ressource est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre de la ressource doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le titre de la ressource ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $title = null;

    #[ORM\Column(name: 'media_type', type: 'string', nullable: false)]
    #[Assert\NotBlank(message: 'Le type de ressource est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: 'Le type de ressource doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le type de ressource ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $mediaType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Url(message: 'Veuillez renseigner une URL valide.')]
    #[Assert\Length(
        max: 2000,
        maxMessage: 'L URL ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $url = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(
        max: 4000,
        maxMessage: 'Le contenu ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $content = null;

    #[ORM\Column(name: 'duration_seconds', type: 'integer', nullable: true)]
    #[Assert\Positive(message: 'La duree doit etre un nombre positif.')]
    #[Assert\LessThanOrEqual(
        value: 14400,
        message: 'La duree ne peut pas depasser 4 heures.'
    )]
    private ?int $durationSeconds = null;

    #[ORM\ManyToOne(inversedBy: 'resources', targetEntity: Exercise::class)]
    #[ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Assert\NotNull(message: 'Vous devez associer la ressource a un exercice.')]
    private ?Exercise $exercise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): static
    {
        $this->mediaType = $mediaType;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDurationSeconds(): ?int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(?int $durationSeconds): static
    {
        $this->durationSeconds = $durationSeconds;

        return $this;
    }

    public function getExercise(): ?Exercise
    {
        return $this->exercise;
    }

    public function setExercise(?Exercise $exercise): static
    {
        $this->exercise = $exercise;

        return $this;
    }

    public function getExerciseId(): ?int
    {
        return $this->exercise?->getId();
    }

    public function __toString(): string
    {
        return $this->title ?? 'Ressource';
    }
}
