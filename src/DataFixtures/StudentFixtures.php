<?php

namespace App\DataFixtures;

use App\Entity\Path;
use App\Entity\Project;
use App\Entity\Student;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StudentFixtures extends Fixture implements DependentFixtureInterface
{
    private const PROJECT_IDS = [746, 639, 637, 638, 675, 676, 677];

    public function getDependencies(): array
    {
        return [
            PathFixtures::class,
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user');

        foreach ($this->getData() as $data) {
            /** @var Path $path */
            $path = $this->getReference($data[4]);
            /** @var Project $project */
            $project = $this->getReference(self::PROJECT_IDS[array_rand(self::PROJECT_IDS)]);

            $student = (new Student())
                ->setIdOC($data[0])
                ->setName($data[1])
                ->setEmail($data[2])
                ->setFunded($data[3])
                ->setPath($path)
                ->setProject($project)
                ->setUser($user)
            ;

            $path->addStudent($student);
            $user->addStudent($student);

            $manager->persist($student);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [8789174, 'Jean Dupont', 'jean.dupont@gmail.com', true, 185],
            [8989173, 'Laure Lefebvre', 'laure.lefebvre@gmail.com', false, 185],
            [8789170, 'Mathilde Boulanger', 'mathilde.boulanger@gmail.com', true, 185],
            [8789141, 'Arthur Dujardin', 'arthur.dujardin@gmail.com', false, 68],
            [8790151, 'Julie Louvois', 'julie.louvois@gmail.com', true, 68],
            [9389141, 'Baptiste Martin', 'baptiste.martin@gmail.com', true, 173],
        ];
    }
}
