<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Evaluation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Evaluation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evaluation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evaluation[]    findAll()
 * @method Evaluation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EvaluationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evaluation::class);
    }

    public function findByMonth(string $yearAndMonth, ?UserInterface $user = null): array
    {
        try {
            $startDate = new DateTime(sprintf('%s-01 00:00:00', $yearAndMonth));
            $endDate = (clone $startDate)->modify('+1 month -1 second');
        } catch (Exception $e) {
            return [];
        }

        $monthEvaluations = $this->createQueryBuilder('evaluation')
            ->where('evaluation.startAt BETWEEN :startDate AND :endDate')
        ;

        $params = ['startDate' => $startDate, 'endDate' => $endDate];

        if ($user) {
            $monthEvaluations->andWhere('evaluation.user = :user');
            $params = array_merge($params, ['user' => $user]);
        }

        $monthEvaluations
            ->setParameters($params)
            ->orderBy('evaluation.id', 'DESC')
        ;

        return $monthEvaluations->getQuery()->getResult();
    }
}
