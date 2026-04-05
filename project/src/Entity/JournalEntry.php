<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    #[Assert\NotBlank(message: 'Title is required.')]
    #[Assert\Length(
    min: 5,
    max: 255,
    minMessage: 'Title must contain at least {{ limit }} characters.',
    maxMessage: 'Title cannot exceed {{ limit }} characters.'
    )]
    #[Assert\Regex(
    pattern: '/.*[A-Za-zÀ-ÿ].*/u',
    message: 'Title must contain at least one letter.'
    )]
    #[Assert\Regex(
    pattern: "/^[A-Za-zÀ-ÿ0-9\\s'.,!?:()\\-]+$/u",
    message: 'Title contains invalid characters.'
    )]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Content is required.')]
    #[Assert\Length(
    min: 10,
    minMessage: 'Content must contain at least {{ limit }} characters.'
    )]
    private string $content = '';

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

    public function setTitle(?string $title): static
    {
        $this->title = trim((string) $title);

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = trim((string) $content);

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