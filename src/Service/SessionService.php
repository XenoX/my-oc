<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Session;
use App\Entity\Student;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SessionService.
 */
class SessionService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * SessionService constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function hydrate(Session $session): Session
    {
        if ($student = $session->getStudent()) {
            $student->addSession($session);
        }

        if ($project = $student->getProject()) {
            $session
                ->setRate($project->getRate())
                ->setProject($project)
            ;
        }

        $session->setDuration($session->getDurationInterval());

        return $session;
    }

    public function createSessionForStudent(Student $student, bool $isEvaluation = false, bool $isNoShow = false): Session
    {
        $project = $student->getProject();
        $rate = $project ? $project->getRate() : 0;

        $session = new Session();
        $session
            ->setStudent($student)
            ->setProject($project)
            ->setRate($isEvaluation ? 0 : $rate)
            ->setDuration($isEvaluation ? new DateInterval(Session::EVALUATION_DURATION) : $session->getDurationInterval())
            ->setNoShow($isNoShow)
            ->setEvaluation($isEvaluation)
        ;

        $student->addSession($session);

        $this->manager->persist($session);

        return $session;
    }

    public function delete(Session $session): void
    {
        if ($student = $session->getStudent()) {
            $student->removeSession($session);
        }

        $this->manager->remove($session);
    }
}
