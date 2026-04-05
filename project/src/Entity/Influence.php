<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'influence')]
class Influence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true, length: 60)]
    private string $name;

    /**
     * @var Collection<int, MoodEntry>
     */
    #[ORM\ManyToMany(targetEntity: MoodEntry::class, mappedBy: 'influences')]
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

    public function setName(string $name): static
    {
        $this->name = $name;

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
}
