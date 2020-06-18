<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Evaluation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

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
     * @var Security
     */
    private $security;

    /**
     * EvaluationService constructor.
     */
    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    /**
     * @return ?Evaluation
     */
    public function hydrate(Evaluation $evaluation): ?Evaluation
    {
        $project = $evaluation->getProject();

        $evaluation->setRate($project ? $project->getRate() : 0);

        $evaluation->setUser($this->security->getUser());

        return $evaluation;
    }

    public function delete(Evaluation $evaluation): void
    {
        $this->manager->remove($evaluation);
    }
}
