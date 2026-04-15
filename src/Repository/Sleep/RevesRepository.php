<?php

namespace App\Repository\Sleep; // ✅ namespace corrigé

use App\Entity\Sleep\Reves;     // ✅ import corrigé
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
            ->leftJoin('r.sommeil', 's') // ✅ r.sommeil (plus r.sommeil_id)
            ->addSelect('s');

        if (!empty($filters['q'])) {
            $qb->andWhere('
                r.titre LIKE :q
                OR r.description LIKE :q
                OR r.emotions LIKE :q
                OR r.typeReve LIKE :q
                OR r.humeur LIKE :q
                OR r.symboles LIKE :q
            ')                           // ✅ r.typeReve camelCase
            ->setParameter('q', '%' . $filters['q'] . '%');
        }

        if (!empty($filters['type'])) {
            $qb->andWhere('r.typeReve = :type') // ✅ camelCase
            ->setParameter('type', $filters['type']);
        }

        if (isset($filters['recurrent']) && $filters['recurrent'] !== '') {
            $qb->andWhere('r.recurrent = :recurrent')
                ->setParameter('recurrent', (bool) $filters['recurrent']);
        }

        if ($filters['couleur'] !== null && $filters['couleur'] !== '') {
            $qb->andWhere('r.couleur = :couleur')
                ->setParameter('couleur', (bool) $filters['couleur']);
        }

        if (!empty($filters['cauchemars']) && $filters['cauchemars'] == '1') {
            $qb->andWhere('LOWER(r.typeReve) = :nightmare') // ✅ camelCase
            ->setParameter('nightmare', 'cauchemar');
        }

        $allowedSorts = [
            'date'      => 'r.createdAt',  // ✅ camelCase
            'titre'     => 'r.titre',
            'type'      => 'r.typeReve',   // ✅ camelCase
            'humeur'    => 'r.humeur',
            'intensite' => 'r.intensite',
        ];

        $sort      = $filters['sort'] ?? 'date';
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
            ->andWhere('LOWER(r.typeReve) = :nightmare') // ✅ camelCase
            ->setParameter('nightmare', 'cauchemar')
            ->getQuery()
            ->getSingleScalarResult();

        $avgIntensity = $this->createQueryBuilder('r')
            ->select('AVG(r.intensite)')
            ->getQuery()
            ->getSingleScalarResult();

        return [
            'total'         => (int) $total,
            'nightmares'    => (int) $nightmares,
            'avg_intensity' => $avgIntensity !== null ? round((float) $avgIntensity, 1) : 0,
            'attention'     => (int) $nightmares >= 3,
        ];
    }
}