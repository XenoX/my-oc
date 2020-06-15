<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Evaluation;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EvaluationService.
 */
class EvaluationService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * EvaluationService constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @return ?Evaluation
     */
    public function hydrate(Evaluation $evaluation): ?Evaluation
    {
        $project = $evaluation->getProject();

        $evaluation->setRate($project ? $project->getRate() : 0);

        if ($project) {
            $project->addEvaluation($evaluation);
        }

        return $evaluation;
    }

    public function delete(Evaluation $evaluation): void
    {
        if ($project = $evaluation->getProject()) {
            $project->removeEvaluation($evaluation);
        }

        $this->manager->remove($evaluation);
    }
}
