<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

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
     * StudentService constructor.
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function hydrate(Student $student): Student
    {
        if ($path = $student->getPath()) {
            $path->addStudent($student);
        }

        return $student;
    }

    public function delete(Student $student): void
    {
        if ($path = $student->getPath()) {
            $path->removeStudent($student);
        }

        $this->manager->remove($student);
    }
}
