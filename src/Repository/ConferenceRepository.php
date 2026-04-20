<?php

namespace App\Repository;

use App\Entity\Conference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conference>
 */
class ConferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conference::class);
    }

    /**
     * @return Conference[]
     */
    public function findRunningConferences(int $limit = 5): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate <= :now')
            ->andWhere('c.endDate >= :now')
            ->setParameter('now', $now)
            ->orderBy('c.startDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Conference[]
     */
    public function findUpcomingConferences(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.startDate >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('c.startDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
