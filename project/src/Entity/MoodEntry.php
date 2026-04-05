<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'mood_entry')]
#[ORM\Index(name: 'idx_mood_entry_user_date', columns: ['user_id', 'entry_date'])]
class MoodEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 36, name: 'user_id')]
    private string $userId;

    #[ORM\Column(type: 'datetime', name: 'entry_date')]
    private \DateTimeInterface $entryDate;

    #[ORM\Column(type: 'string', columnDefinition: "ENUM('MOMENT','DAY')", name: 'moment_type')]
    private string $momentType;

    #[ORM\Column(type: 'smallint', name: 'mood_level')]
    private int $moodLevel;

    #[ORM\Column(type: 'datetime', name: 'updated_at')]
    private \DateTimeInterface $updatedAt;

   /**
    * @var Collection<int, Emotion>
    */
   #[Assert\Count(
    min: 1,
    max: 5,
    minMessage: 'Select at least one emotion.',
    maxMessage: 'You can select at most {{ limit }} emotions.'
   )]
    #[ORM\ManyToMany(targetEntity: Emotion::class, inversedBy: 'moodEntries')]
    #[ORM\JoinTable(name: 'mood_entry_emotion')]
    #[ORM\JoinColumn(name: 'mood_entry_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'emotion_id', referencedColumnName: 'id')]
    private Collection $emotions;
    
    
    /**
    * @var Collection<int, Influence>
    */
    #[Assert\Count(
    min: 1,
    max: 5,
    minMessage: 'Select at least one influence.',
    maxMessage: 'You can select at most {{ limit }} influences.'
    )]
    #[ORM\ManyToMany(targetEntity: Influence::class, inversedBy: 'moodEntries')]
    #[ORM\JoinTable(name: 'mood_entry_influence')]
    #[ORM\JoinColumn(name: 'mood_entry_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'influence_id', referencedColumnName: 'id')]
    private Collection $influences;

    public function __construct()
    {
        $this->emotions = new ArrayCollection();
        $this->influences = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getEntryDate(): \DateTimeInterface
    {
        return $this->entryDate;
    }

    public function setEntryDate(\DateTimeInterface $entryDate): static
    {
        $this->entryDate = $entryDate;

        return $this;
    }

    public function getMomentType(): string
    {
        return $this->momentType;
    }

    public function setMomentType(string $momentType): static
    {
        $this->momentType = $momentType;

        return $this;
    }

    public function getMoodLevel(): int
    {
        return $this->moodLevel;
    }

    public function setMoodLevel(int $moodLevel): static
    {
        $this->moodLevel = $moodLevel;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Emotion>
     */
    public function getEmotions(): Collection
    {
        return $this->emotions;
    }

    public function addEmotion(Emotion $emotion): static
    {
        if (!$this->emotions->contains($emotion)) {
            $this->emotions->add($emotion);
            $emotion->addMoodEntry($this);
        }

        return $this;
    }

    public function removeEmotion(Emotion $emotion): static
    {
        $this->emotions->removeElement($emotion);
        $emotion->removeMoodEntry($this);

        return $this;
    }

    /**
     * @return Collection<int, Influence>
     */
    public function getInfluences(): Collection
    {
        return $this->influences;
    }

    public function addInfluence(Influence $influence): static
    {
        if (!$this->influences->contains($influence)) {
            $this->influences->add($influence);
            $influence->addMoodEntry($this);
        }

        return $this;
    }

    public function removeInfluence(Influence $influence): static
    {
        $this->influences->removeElement($influence);
        $influence->removeMoodEntry($this);

        return $this;
    }
}
