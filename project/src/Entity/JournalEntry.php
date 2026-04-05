<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'journal_entry')]
#[ORM\Index(name: 'idx_journal_user_created', columns: ['user_id', 'created_at'])]
class JournalEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 36, name: 'user_id')]
    private string $userId;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', name: 'updated_at')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'text', nullable: true, name: 'ai_tags')]
    private ?string $aiTags = null;

    #[ORM\Column(type: 'string', nullable: true, length: 32, name: 'ai_model_version')]
    private ?string $aiModelVersion = null;

    #[ORM\Column(type: 'datetime', nullable: true, name: 'ai_generated_at')]
    private ?\DateTimeInterface $aiGeneratedAt = null;

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getAiTags(): ?string
    {
        return $this->aiTags;
    }

    public function setAiTags(?string $aiTags): static
    {
        $this->aiTags = $aiTags;

        return $this;
    }

    public function getAiModelVersion(): ?string
    {
        return $this->aiModelVersion;
    }

    public function setAiModelVersion(?string $aiModelVersion): static
    {
        $this->aiModelVersion = $aiModelVersion;

        return $this;
    }

    public function getAiGeneratedAt(): ?\DateTimeInterface
    {
        return $this->aiGeneratedAt;
    }

    public function setAiGeneratedAt(?\DateTimeInterface $aiGeneratedAt): static
    {
        $this->aiGeneratedAt = $aiGeneratedAt;

        return $this;
    }
}
