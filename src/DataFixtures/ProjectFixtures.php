<?php

namespace App\DataFixtures;

use App\Entity\Path;
use App\Entity\Project;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            PathFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            /** @var Path $path */
            $path = $this->getReference($data[3]);

            $project = (new Project())
                ->setIdOC($data[0])
                ->setName($data[1])
                ->setDescription('Fixture description.')
                ->setEvaluation($data[2])
                ->setDuration(new DateInterval('PT20H'))
                ->setLanguage('fr')
                ->setLink('https://openclassrooms.com/')
                ->setRate(random_int(1, 3))
                ->setPath($path)
            ;

            $path->addProject($project);

            $manager->persist($project);

            $this->setReference($data[0], $project);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [746, 'Découvrez le quotidien d\'un développeur web', 'mentor', 185],
            [639, 'Transformez votre CV en site Web', 'validator', 185],
            [637, 'Dynamisez une page web avec des animations CSS', 'validator', 185],
            [638, 'Optimisez un site web existant', 'validator', 68],
            [675, 'Construisez un site e-commerce', 'validator', 68],
            [676, 'Construisez une API sécurisée pour une application d\'avis gastronomiques', 'validator', 68],
            [677, 'Créez un réseau social d’entreprise', 'validator', 173],
        ];
    }
}
