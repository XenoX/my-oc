<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Path;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class PathService.
 */
class PathService
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ProjectService
     */
    private $projectService;

    /**
     * PathService constructor.
     */
    public function __construct(EntityManagerInterface $manager, ProjectService $projectService)
    {
        $this->manager = $manager;
        $this->projectService = $projectService;
    }

    public function createOrUpdate(array $pathData): Path
    {
        return $this->hydrate(
            $this->manager->getRepository(Path::class)->findOneBy(['idOC' => $pathData['id']]) ?? new Path(),
            $pathData
        );
    }

    public function delete(Path $path): void
    {
        foreach ($path->getProjects() as $project) {
            $project->removePath($path);
        }

        $this->manager->remove($path);
    }

    private function hydrate(Path $path, array $pathData): Path
    {
        try {
            $dateInterval = new DateInterval($pathData['duration']);
        } catch (Exception $e) {
            $dateInterval = new DateInterval('P12M');
        }

        $path
            ->setIdOC($pathData['id'])
            ->setName($pathData['title'])
            ->setDescription($pathData['description'])
            ->setImage($pathData['illustration'])
            ->setDuration($dateInterval)
            ->setLanguage($pathData['language'])
            ->setLink($pathData['OpenClassroomsUrl'])
        ;

        foreach ($pathData['projects'] as $project) {
            $project = $this->projectService->createOrUpdate($project, $path->getName());

            $path->addProject($project);
            $project->addPath($path);
        }

        return $path;
    }
}
