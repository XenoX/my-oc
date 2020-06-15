<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects")
 *
 * Class ProjectController
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/{id}/rate/{direction}", requirements={"direction": "up|down"})
     */
    public function changeRate(Request $request, Project $project, string $direction): RedirectResponse
    {
        $rate = $project->getRate();

        if ((3 <= $rate && 'up' === $direction) || (1 >= $rate && 'down' === $direction)) {
            return $this->redirect($request->headers->get('referer'));
        }

        $project->setRate('up' === $direction ? ++$rate : --$rate);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->headers->get('referer'));
    }
}
