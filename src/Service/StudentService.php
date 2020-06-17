<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class StudentService.
 */
class StudentService
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
     * StudentService constructor.
     */
    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->manager = $manager;
        $this->security = $security;
    }

    public function hydrate(Student $student): Student
    {
        if ($path = $student->getPath()) {
            $path->addStudent($student);
        }

        /** @var User $user */
        if ($user = $this->security->getUser()) {
            $student->setUser($user);
            $user->addStudent($student);
        }

        return $student;
    }

    public function delete(Student $student): void
    {
        if ($path = $student->getPath()) {
            $path->removeStudent($student);
        }

        /** @var User $user */
        if ($user = $this->security->getUser()) {
            $user->removeStudent($student);
        }

        $this->manager->remove($student);
    }
}
