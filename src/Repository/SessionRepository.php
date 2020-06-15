<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Session;
use App\Entity\Student;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function findByMonth(string $yearAndMonth, ?Student $student = null, bool $withEvaluation = true): array
    {
        try {
            $startDate = new DateTime(sprintf('%s-01 00:00:00', $yearAndMonth));
            $endDate = (clone $startDate)->modify('+1 month -1 second');
        } catch (Exception $e) {
            return [];
        }

        $monthSessions = $this->createQueryBuilder('session')
            ->where('session.startAt BETWEEN :startDate AND :endDate')
            ->orderBy('session.id', 'DESC')
        ;

        $params = ['startDate' => $startDate, 'endDate' => $endDate];

        if ($student) {
            $monthSessions->andWhere('session.student = :student');
            $params = array_merge($params, ['student' => $student]);
        }

        if (!$withEvaluation) {
            $monthSessions->andWhere('session.evaluation = 0');
        }

        $monthSessions->setParameters($params);

        return $monthSessions->getQuery()->getResult();
    }

    public function countEvalForMonth(string $yearAndMonth): int
    {
        try {
            $startDate = new DateTime(sprintf('%s-01 00:00:00', $yearAndMonth));
            $endDate = (clone $startDate)->modify('+1 month -1 second');
        } catch (Exception $e) {
            return 0;
        }

        $monthEvaluationsCount = $this->createQueryBuilder('session')
            ->select('COUNT(session.id)')
            ->where('session.startAt BETWEEN :startDate AND :endDate')
            ->andWhere('session.evaluation = 1')
            ->setParameters(['startDate' => $startDate, 'endDate' => $endDate])
        ;

        try {
            return (int) $monthEvaluationsCount->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }
}
