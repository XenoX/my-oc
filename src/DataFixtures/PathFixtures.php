<?php

namespace App\DataFixtures;

use App\Entity\Path;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PathFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as $data) {
            $object = (new Path())
                ->setIdOC($data[0])
                ->setName($data[1])
                ->setDescription('Fixture description.')
                ->setImage('https://static.oc-static.com/prod/images/paths/illustrations/61/15435992831411_path61.png')
                ->setDuration(new \DateInterval('P12M'))
                ->setLanguage('fr')
                ->setLink('https://openclassrooms.com/fr/paths/'.$data[0])
            ;

            if (isset($data[2])) {
                foreach ($data[2] as $projectId) {
                    /** @var Project $project */
                    $project = $this->getReference($projectId);

                    $object->addProject($project);
                    $project->addPath($object);
                }
            }

            $manager->persist($object);

            $this->setReference($data[0], $object);
        }

        $manager->flush();
    }

    private function getData(): array
    {
        return [
            [185, 'Développeur Web', [746, 639, 637, 638, 675, 676, 677]],
            [68, 'Développeur d\'application - Python', [746, 639, 637, 638, 675, 676, 677]],
            [173, 'Commercial - Chargé d\'affaires', [746, 639, 637, 638, 675, 676, 677]],
            [65, 'Data Analyst'],
            [84, 'Community Manager'],
            [169, 'Développeur Salesforce'],
        ];
    }
}
