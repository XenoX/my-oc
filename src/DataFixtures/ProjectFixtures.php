<?php

namespace App\DataFixtures;

use App\Entity\Project;
use DateInterval;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $object = (new Project())
                ->setIdOC($data[0])
                ->setName($data[1])
                ->setDescription('Fixture description.')
                ->setEvaluation($data[2])
                ->setDuration(new DateInterval('PT20H'))
                ->setLanguage('fr')
                ->setLink('https://openclassrooms.com/')
                ->setRate(random_int(1, 3))
            ;

            $manager->persist($object);

            $this->setReference($data[0], $object);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [746, 'Découvrez le quotidien d\'un développeur web', 'mentor'],
            [639, 'Transformez votre CV en site Web', 'validator'],
            [637, 'Dynamisez une page web avec des animations CSS', 'validator'],
            [638, 'Optimisez un site web existant', 'validator'],
            [675, 'Construisez un site e-commerce', 'validator'],
            [676, 'Construisez une API sécurisée pour une application d\'avis gastronomiques', 'validator'],
            [677, 'Créez un réseau social d’entreprise', 'validator'],
        ];
    }
}
