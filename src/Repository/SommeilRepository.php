<?php

namespace App\Repository;

use App\Entity\Sommeil;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SommeilRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sommeil::class);
    }
    public function findFrontFiltered(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('s');

        if (!empty($filters['q'])) {
            $qb->andWhere('s.commentaire LIKE :q OR s.qualite LIKE :q OR s.humeur_reveil LIKE :q OR s.environnement LIKE :q')
                ->setParameter('q', '%' . $filters['q'] . '%');
        }

        if (!empty($filters['qualite'])) {
            $qb->andWhere('s.qualite = :qualite')
                ->setParameter('qualite', $filters['qualite']);
        }

        if (!empty($filters['humeur'])) {
            $qb->andWhere('s.humeur_reveil = :humeur')
                ->setParameter('humeur', $filters['humeur']);
        }

        if (!empty($filters['insuffisant']) && $filters['insuffisant'] == '1') {
            $qb->andWhere('s.duree_sommeil < :minSleep')
                ->setParameter('minSleep', 5);
        }

        $allowedSorts = [
            'date_nuit' => 's.date_nuit',
            'duree' => 's.duree_sommeil',
            'qualite' => 's.qualite',
            'interruptions' => 's.interruptions',
        ];

        $sort = $filters['sort'] ?? 'date_nuit';
        $direction = strtoupper($filters['direction'] ?? 'DESC');
        $direction = in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'DESC';

        if (!isset($allowedSorts[$sort])) {
            $sort = 'date_nuit';
        }

        $qb->orderBy($allowedSorts[$sort], $direction);

        return $qb->getQuery()->getResult();
    }
    public function getFrontStats(): array
    {
        $qb = $this->createQueryBuilder('s');

        $total = (clone $qb)
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $avgDuration = $this->createQueryBuilder('s')
            ->select('AVG(s.duree_sommeil)')
            ->getQuery()
            ->getSingleScalarResult();

        $insufficient = $this->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->andWhere('s.duree_sommeil < :minSleep')
            ->setParameter('minSleep', 5)
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => (int) $total,
            'avg_duration' => $avgDuration !== null ? round((float) $avgDuration, 1) : 0,
            'insufficient' => (int) $insufficient,
        ];
    }
}