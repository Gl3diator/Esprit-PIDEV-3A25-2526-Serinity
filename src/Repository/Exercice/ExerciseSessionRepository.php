<?php

declare(strict_types=1);

namespace App\Repository\Exercice;

use App\Entity\Exercice\ExerciseSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ExerciseSession> */
class ExerciseSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExerciseSession::class);
    }
}