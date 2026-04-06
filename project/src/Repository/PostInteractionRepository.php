<?php

namespace App\Repository;

use App\Entity\ForumThread;
use App\Entity\PostInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PostInteraction>
 */
class PostInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostInteraction::class);
    }

    public function findOneForUser(ForumThread $thread, string $userId): ?PostInteraction
    {
        return $this->findOneBy(['thread' => $thread, 'userId' => $userId]);
    }

    /**
     * @return ForumThread[]
     */
    public function findFollowedThreadsForUser(string $userId): array
    {
        $rows = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from(ForumThread::class, 't')
            ->innerJoin(PostInteraction::class, 'pi', 'WITH', 'pi.thread = t')
            ->andWhere('pi.userId = :userId')
            ->andWhere('pi.follow = true')
            ->setParameter('userId', $userId)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $rows;
    }
}
