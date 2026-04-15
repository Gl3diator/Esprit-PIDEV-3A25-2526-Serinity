<?php

declare(strict_types=1);

namespace App\Repository\Exercice; // ✅ était: App\Repository\Exercice\ExerciseRepository

use App\Entity\Exercice\Exercise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Exercise> */
class ExerciseRepository extends ServiceEntityRepository
{
    private const SORT_MAP = [
        'title_asc'    => ['e.title', 'ASC'],
        'title_desc'   => ['e.title', 'DESC'],
        'duration_asc' => ['e.durationMinutes', 'ASC'],
        'duration_desc'=> ['e.durationMinutes', 'DESC'],
        'level_asc'    => ['e.level', 'ASC'],
        'level_desc'   => ['e.level', 'DESC'],
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exercise::class);
    }

    /** @return Exercise[] */
    public function findForFront(?string $search, ?string $type, ?int $level, ?string $sort): array
    {
        $qb = $this->createQueryBuilder('e');

        if ($search !== null && $search !== '') {
            $qb->andWhere('LOWER(e.title) LIKE LOWER(:search)')
                ->setParameter('search', '%' . trim($search) . '%');
        }
        if ($type !== null && $type !== '') {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }
        if ($level !== null) {
            $qb->andWhere('e.level = :level')->setParameter('level', $level);
        }

        [$field, $direction] = self::SORT_MAP[$sort] ?? ['e.title', 'ASC'];

        return $qb->orderBy($field, $direction)
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @return string[] */
    public function findAvailableTypes(): array
    {
        $rows = $this->createQueryBuilder('e')
            ->select('DISTINCT e.type AS type')
            ->where('e.type IS NOT NULL')
            ->orderBy('e.type', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_values(array_filter(
            array_map(static fn(array $row): ?string => $row['type'] ?? null, $rows)
        ));
    }

    /** @return int[] */
    public function findAvailableLevels(): array
    {
        $rows = $this->createQueryBuilder('e')
            ->select('DISTINCT e.level AS level')
            ->where('e.level IS NOT NULL')
            ->orderBy('e.level', 'ASC')
            ->getQuery()
            ->getArrayResult();

        return array_values(
            array_map(static fn(array $row): int => (int) $row['level'], $rows)
        );
    }
}