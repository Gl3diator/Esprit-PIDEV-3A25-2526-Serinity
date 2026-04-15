<?php

declare(strict_types=1);

namespace App\Entity\Exercice;

use App\Entity\Access\User; // ✅ import manquant
use App\Repository\Exercice\ExerciseSessionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExerciseSessionRepository::class)]
#[ORM\Table(name: 'exercise_session')]
class ExerciseSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "L'utilisateur est obligatoire.")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Exercise::class)]
    #[ORM\JoinColumn(name: 'exercise_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "L'exercice est obligatoire.")]
    private ?Exercise $exercise = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    #[Assert\Length(max: 100, maxMessage: 'Le statut ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $status = null;

    #[ORM\Column(name: 'started_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(name: 'completed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000, maxMessage: 'Le feedback ne doit pas dépasser {{ limit }} caractères.')]
    private ?string $feedback = null;

    #[ORM\Column(name: 'active_seconds', type: 'integer', nullable: false, options: ['default' => 0])]
    #[Assert\PositiveOrZero(message: 'Le nombre de secondes actives doit être positif ou nul.')]
    private int $activeSeconds = 0;

    #[ORM\Column(name: 'last_resumed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastResumedAt = null;

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getExercise(): ?Exercise { return $this->exercise; }
    public function setExercise(?Exercise $exercise): static { $this->exercise = $exercise; return $this; }

    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getStartedAt(): ?\DateTimeInterface { return $this->startedAt; }
    public function setStartedAt(?\DateTimeInterface $startedAt): static { $this->startedAt = $startedAt; return $this; }

    public function getCompletedAt(): ?\DateTimeInterface { return $this->completedAt; }
    public function setCompletedAt(?\DateTimeInterface $completedAt): static { $this->completedAt = $completedAt; return $this; }

    public function getFeedback(): ?string { return $this->feedback; }
    public function setFeedback(?string $feedback): static { $this->feedback = $feedback; return $this; }

    public function getActiveSeconds(): int { return $this->activeSeconds; }
    public function setActiveSeconds(int $activeSeconds): static { $this->activeSeconds = $activeSeconds; return $this; }

    public function getLastResumedAt(): ?\DateTimeInterface { return $this->lastResumedAt; }
    public function setLastResumedAt(?\DateTimeInterface $lastResumedAt): static { $this->lastResumedAt = $lastResumedAt; return $this; }
}