<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use DateInterval;
use Exception;

/**
 * Class ProjectService.
 */
class ProjectService
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * @var RateService
     */
    private $rateService;

    /**
     * ProjectService constructor.
     */
    public function __construct(ProjectRepository $projectRepository, RateService $rateService)
    {
        $this->projectRepository = $projectRepository;
        $this->rateService = $rateService;
    }

    public function createOrUpdate(array $projectData, string $pathName): Project
    {
        return $this->hydrate(
            $this->projectRepository->findOneBy(['idOC' => $projectData['id']]) ?? new Project(),
            $projectData,
            $pathName
        );
    }

    private function hydrate(Project $project, array $projectData, string $pathName): Project
    {
        try {
            $dateInterval = new DateInterval($projectData['duration']);
        } catch (Exception $e) {
            $dateInterval = new DateInterval('PT20H');
        }

        return $project
            ->setIdOC($projectData['id'])
            ->setName($projectData['title'])
            ->setDescription($projectData['shortDescription'])
            ->setDuration($dateInterval)
            ->setLanguage($projectData['language'])
            ->setEvaluation($projectData['evaluatorType'])
            ->setLink($projectData['OpenClassroomsUrl'])
            ->setRate($this->rateService->getRateForPathAndProject($pathName, $projectData['title']))
        ;
    }
}
