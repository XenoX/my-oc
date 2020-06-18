<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Path;
use App\Entity\User;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Security;

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
     * @var Security
     */
    private $security;

    /**
     * PathService constructor.
     */
    public function __construct(EntityManagerInterface $manager, ProjectService $projectService, Security $security)
    {
        $this->manager = $manager;
        $this->projectService = $projectService;
        $this->security = $security;
    }

    public function createOrUpdate(array $pathData): Path
    {
        return $this->hydrate(
            $this->manager->getRepository(Path::class)->findOneBy([
                'idOC' => $pathData['id'],
                'user' => $this->security->getUser(),
            ]) ?? new Path(),
            $pathData
        );
    }

    public function delete(Path $path): void
    {
        if ($user = $path->getUser()) {
            $user->removePath($path);
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

        /** @var User $user */
        $user = $this->security->getUser();

        $path
            ->setIdOC($pathData['id'])
            ->setName($pathData['title'])
            ->setDescription($pathData['description'])
            ->setImage($pathData['illustration'])
            ->setDuration($dateInterval)
            ->setLanguage($pathData['language'])
            ->setLink($pathData['OpenClassroomsUrl'])
            ->setUser($user)
        ;

        foreach ($pathData['projects'] as $projectData) {
            $project = $this->projectService->hydrate($projectData, $path);

            $path->addProject($project);
        }

        return $path;
    }
}
