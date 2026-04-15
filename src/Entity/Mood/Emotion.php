<?php

declare(strict_types=1);

namespace App\Entity\Mood;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'emotion')]
#[UniqueEntity(
    fields: ['name'],
    message: 'This emotion already exists.'
)]
class Emotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true, length: 40)]
    #[Assert\NotBlank(message: 'Emotion name is required.')]
    #[Assert\Length(
    max: 40,
    min: 3,
    maxMessage: 'Emotion name cannot exceed {{ limit }} characters.',
    minMessage: 'Emotion name must contain at least {{ limit }} characters.'
    )]
    #[Assert\Regex(
        pattern: '/^[A-Za-z ]+$/',
        message: 'Emotion name can contain only letters and spaces.'
    )]
    private string $name = '';

    /**
     * @var Collection<int, MoodEntry>
     */
    #[ORM\ManyToMany(targetEntity: MoodEntry::class, mappedBy: 'emotions')]
    private Collection $moodEntries;

    public function __construct()
    {
        $this->moodEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = trim($name);

        return $this;
    }

    /**
     * @return Collection<int, MoodEntry>
     */
    public function getMoodEntries(): Collection
    {
        return $this->moodEntries;
    }

    public function addMoodEntry(MoodEntry $moodEntry): static
    {
        if (!$this->moodEntries->contains($moodEntry)) {
            $this->moodEntries->add($moodEntry);
        }

        return $this;
    }

    public function removeMoodEntry(MoodEntry $moodEntry): static
    {
        $this->moodEntries->removeElement($moodEntry);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}