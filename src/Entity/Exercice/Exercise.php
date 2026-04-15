<?php

declare(strict_types=1);

namespace App\Entity\Exercice; // ✅ était: App\Entity\Exercice\Exercise

use App\Repository\Exercice\ExerciseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExerciseRepository::class)]
#[ORM\Table(name: 'exercise')]
class Exercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Le titre est obligatoire.')]
    #[Assert\Length(min: 3, max: 255,
        minMessage: 'Le titre doit contenir au moins {{ limit }} caracteres.',
        maxMessage: 'Le titre ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: 'Le type est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le type ne doit pas depasser {{ limit }} caracteres.')]
    private ?string $type = null;

    #[ORM\Column(type: 'integer', nullable: false)]
    #[Assert\NotNull(message: 'Le niveau est obligatoire.')]
    #[Assert\Positive(message: 'Le niveau doit etre un nombre positif.')]
    #[Assert\LessThanOrEqual(value: 10, message: 'Le niveau ne peut pas depasser 10.')]
    private ?int $level = null;

    #[ORM\Column(name: 'duration_minutes', type: 'integer', nullable: false)]
    #[Assert\NotNull(message: 'La duree est obligatoire.')]
    #[Assert\Positive(message: 'La duree doit etre un nombre positif.')]
    #[Assert\LessThanOrEqual(value: 300, message: 'La duree ne peut pas depasser 300 minutes.')]
    private ?int $durationMinutes = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'La description ne doit pas depasser {{ limit }} caracteres.')]
    private ?string $description = null;

    /** @var Collection<int, Resource> */
    #[ORM\OneToMany(mappedBy: 'exercise', targetEntity: Resource::class)]
    private Collection $resources;

    public function __construct()
    {
        $this->resources = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getLevel(): ?int { return $this->level; }
    public function setLevel(int $level): static { $this->level = $level; return $this; }

    public function getDurationMinutes(): ?int { return $this->durationMinutes; }
    public function setDurationMinutes(int $durationMinutes): static { $this->durationMinutes = $durationMinutes; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    /** @return Collection<int, Resource> */
    public function getResources(): Collection { return $this->resources; }

    public function addResource(Resource $resource): static
    {
        if (!$this->resources->contains($resource)) {
            $this->resources->add($resource);
            $resource->setExercise($this);
        }
        return $this;
    }

    public function removeResource(Resource $resource): static
    {
        if ($this->resources->removeElement($resource) && $resource->getExercise() === $this) {
            $resource->setExercise(null);
        }
        return $this;
    }

    public function __toString(): string { return $this->title ?? 'Exercice'; }
}