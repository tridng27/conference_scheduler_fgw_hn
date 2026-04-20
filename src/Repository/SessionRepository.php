<?php

namespace App\Repository;

use App\Entity\Conference;
use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Session>
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    /**
     * @return Session[]
     */
    public function findSchedule(?int $conferenceId = null, ?\DateTimeImmutable $date = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.conference', 'c')->addSelect('c')
            ->leftJoin('s.rooms', 'r')->addSelect('r')
            ->leftJoin('s.speakers', 'sp')->addSelect('sp')
            ->orderBy('s.startTime', 'ASC')
            ->distinct();

        if (null !== $conferenceId) {
            $qb->andWhere('c.id = :conferenceId')
                ->setParameter('conferenceId', $conferenceId);
        }

        if (null !== $date) {
            $start = $date->setTime(0, 0, 0);
            $end = $date->setTime(23, 59, 59);
            $qb->andWhere('s.startTime BETWEEN :dayStart AND :dayEnd')
                ->setParameter('dayStart', $start)
                ->setParameter('dayEnd', $end);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Session[]
     */
    public function findTodaySchedule(): array
    {
        return $this->findSchedule(null, new \DateTimeImmutable('today'));
    }

    /**
     * @return Session[]
     */
    public function findUpcomingSessions(int $limit = 8): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.conference', 'c')->addSelect('c')
            ->leftJoin('s.rooms', 'r')->addSelect('r')
            ->leftJoin('s.speakers', 'sp')->addSelect('sp')
            ->andWhere('s.startTime >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('s.startTime', 'ASC')
            ->setMaxResults($limit)
            ->distinct()
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Session[]
     */
    public function findByConferenceForConflict(Conference $conference, ?int $excludeId = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->leftJoin('s.rooms', 'r')->addSelect('r')
            ->leftJoin('s.speakers', 'sp')->addSelect('sp')
            ->andWhere('s.conference = :conference')
            ->setParameter('conference', $conference);

        if (null !== $excludeId) {
            $qb->andWhere('s.id != :excludeId')->setParameter('excludeId', $excludeId);
        }

        return $qb->getQuery()->getResult();
    }
}
