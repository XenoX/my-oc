<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Session;
use App\Entity\Student;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

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
     * @var Security
     */
    private $security;

    /**
     * SessionService constructor.
     */
    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
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

        $session
            ->setUser($this->security->getUser())
            ->setDuration($session->getDurationInterval())
        ;

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
            ->setUser($this->security->getUser())
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
