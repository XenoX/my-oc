<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Path;
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
     * @var RateService
     */
    private $rateService;

    /**
     * ProjectService constructor.
     */
    public function __construct(ProjectRepository $projectRepository, RateService $rateService)
    {
        $this->rateService = $rateService;
    }

    public function hydrate(array $projectData, Path $path): Project
    {
        try {
            $dateInterval = new DateInterval($projectData['duration']);
        } catch (Exception $e) {
            $dateInterval = new DateInterval('PT20H');
        }

        return (new Project())
            ->setIdOC($projectData['id'])
            ->setName($projectData['title'])
            ->setDescription($projectData['shortDescription'])
            ->setDuration($dateInterval)
            ->setLanguage($projectData['language'])
            ->setEvaluation($projectData['evaluatorType'])
            ->setLink($projectData['OpenClassroomsUrl'])
            ->setRate($this->rateService->getRateForPathAndProject($path->getName(), $projectData['title']))
            ->setPath($path)
        ;
    }
}
