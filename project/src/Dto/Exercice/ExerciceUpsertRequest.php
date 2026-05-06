<?php

declare(strict_types=1);

namespace App\Dto\Exercice;

use Symfony\Component\Validator\Constraints as Assert;

final class ExerciceUpsertRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $title = '';

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 100)]
    public string $type = '';

    #[Assert\Range(min: 1, max: 10)]
    public int $level = 1;

    #[Assert\Range(min: 1, max: 300)]
    public int $durationMinutes = 10;

    #[Assert\Length(max: 2000)]
    public ?string $description = null;

    #[Assert\Length(max: 3000)]
    public ?string $benefits = null;

    /**
     * @var list<array{title:string,description:string}>|null
     */
    public ?array $guidedInstructions = null;

    #[Assert\Length(max: 3000)]
    public ?string $tips = null;

    #[Assert\Length(max: 512)]
    public ?string $imageUrl = null;

    #[Assert\Length(max: 50)]
    public ?string $theme = null;

    public bool $isActive = true;
}
