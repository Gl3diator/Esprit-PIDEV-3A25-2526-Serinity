<?php

declare(strict_types=1);

namespace App\Repository\Access;

use App\Entity\Access\Profile;
use App\Entity\Access\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Profile> */
class ProfileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profile::class);
    }

    public function findUserByUsername(string $username): ?User
    {
        $profile = $this->findOneBy(['username' => $username]);

        return $profile?->getUser();
    }
}
