<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Session;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

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

    public function findByMonth(string $yearAndMonth, ?UserInterface $user = null): array
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

        if ($user) {
            $monthSessions->andWhere('session.user = :user');
            $params = array_merge($params, ['user' => $user]);
        }

        $monthSessions
            ->setParameters($params)
            ->orderBy('session.id', 'DESC')
        ;

        return $monthSessions->getQuery()->getResult();
    }

    public function countEvalForMonth(string $yearAndMonth, ?UserInterface $user = null): int
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
        ;

        $params = ['startDate' => $startDate, 'endDate' => $endDate];

        if ($user) {
            $monthEvaluationsCount->andWhere('session.user = :user');
            $params = array_merge($params, ['user' => $user]);
        }

        $monthEvaluationsCount->setParameters($params);

        try {
            return (int) $monthEvaluationsCount->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return 0;
        }
    }
}
