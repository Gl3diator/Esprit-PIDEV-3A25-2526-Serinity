<?php

namespace App\Repository;

use App\Entity\Reves;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RevesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reves::class);
    }

    public function findFrontFiltered(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('r')
            ->leftJoin('r.sommeil_id', 's')
            ->addSelect('s');

        // Recherche globale
        if (!empty($filters['q'])) {
            $qb->andWhere('
                r.titre LIKE :q
                OR r.description LIKE :q
                OR r.emotions LIKE :q
                OR r.type_reve LIKE :q
                OR r.humeur LIKE :q
                OR r.symboles LIKE :q
            ')
                ->setParameter('q', '%' . $filters['q'] . '%');
        }

        // Filtre type
        if (!empty($filters['type'])) {
            $qb->andWhere('r.type_reve = :type')
                ->setParameter('type', $filters['type']);
        }

        // Filtre récurrent
        if (isset($filters['recurrent']) && $filters['recurrent'] !== '') {
            $qb->andWhere('r.recurrent = :recurrent')
                ->setParameter('recurrent', (bool) $filters['recurrent']);
        }

        // Filtre couleur
        if ($filters['couleur'] !== null && $filters['couleur'] !== '') {
            $qb->andWhere('r.couleur = :couleur')
                ->setParameter('couleur', (bool) $filters['couleur']);
        }

        // Filtre cauchemars
        if (!empty($filters['cauchemars']) && $filters['cauchemars'] == '1') {
            $qb->andWhere('LOWER(r.type_reve) = :nightmare')
                ->setParameter('nightmare', 'cauchemar');
        }

        // Tri autorisé
        $allowedSorts = [
            'date' => 'r.created_at',
            'titre' => 'r.titre',
            'type' => 'r.type_reve',
            'humeur' => 'r.humeur',
            'intensite' => 'r.intensite',
        ];

        $sort = $filters['sort'] ?? 'date';
        $direction = strtoupper($filters['direction'] ?? 'DESC');
        $direction = in_array($direction, ['ASC', 'DESC'], true) ? $direction : 'DESC';

        if (!isset($allowedSorts[$sort])) {
            $sort = 'date';
        }

        $qb->orderBy($allowedSorts[$sort], $direction);

        return $qb->getQuery()->getResult();
    }

    public function getFrontStats(): array
    {
        $total = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $nightmares = $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->andWhere('LOWER(r.type_reve) = :nightmare')
            ->setParameter('nightmare', 'cauchemar')
            ->getQuery()
            ->getSingleScalarResult();

        $avgIntensity = $this->createQueryBuilder('r')
            ->select('AVG(r.intensite)')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total' => (int) $total,
            'nightmares' => (int) $nightmares,
            'avg_intensity' => $avgIntensity !== null ? round((float) $avgIntensity, 1) : 0,
            'attention' => (int) $nightmares >= 3,
        ];
    }
}