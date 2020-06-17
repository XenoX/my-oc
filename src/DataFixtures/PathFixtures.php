<?php

namespace App\DataFixtures;

use App\Entity\Path;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PathFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        /** @var User $user */
        $user = $this->getReference('user');

        foreach ($this->getData() as $data) {
            $path = (new Path())
                ->setIdOC($data[0])
                ->setName($data[1])
                ->setDescription('Fixture description.')
                ->setImage('https://static.oc-static.com/prod/images/paths/illustrations/61/15435992831411_path61.png')
                ->setDuration(new \DateInterval('P12M'))
                ->setLanguage('fr')
                ->setLink('https://openclassrooms.com/fr/paths/'.$data[0])
                ->setUser($user)
            ;

            $user->addPath($path);

            $manager->persist($path);

            $this->setReference($data[0], $path);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [185, 'Développeur Web'],
            [68, 'Développeur d\'application - Python'],
            [173, 'Commercial - Chargé d\'affaires'],
            [65, 'Data Analyst'],
            [84, 'Community Manager'],
            [169, 'Développeur Salesforce'],
        ];
    }
}
