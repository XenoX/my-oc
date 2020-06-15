<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Evaluation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

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

    public function findByMonth(string $yearAndMonth): array
    {
        try {
            $startDate = new DateTime(sprintf('%s-01 00:00:00', $yearAndMonth));
            $endDate = (clone $startDate)->modify('+1 month -1 second');
        } catch (Exception $e) {
            return [];
        }

        $monthEvaluations = $this->createQueryBuilder('evaluation')
            ->where('evaluation.startAt BETWEEN :startDate AND :endDate')
            ->setParameters(['startDate' => $startDate, 'endDate' => $endDate])
            ->orderBy('evaluation.id', 'DESC')
        ;

        return $monthEvaluations->getQuery()->getResult();
    }
}
