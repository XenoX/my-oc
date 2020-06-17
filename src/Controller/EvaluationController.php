<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evaluation;
use App\Form\EvaluationType;
use App\Repository\EvaluationRepository;
use App\Security\Voter\AppVoter;
use App\Service\EvaluationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evaluations")
 */
class EvaluationController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(SessionInterface $session, EvaluationRepository $evaluationRepository): Response
    {
        return $this->render('evaluation/index.html.twig', [
            'evaluations' => $evaluationRepository->findByMonth(
                $session->get('yearAndMonth', (new \DateTime())->format('Y-m')),
                $this->getUser()
            ),
        ]);
    }

    /**
     * @Route("/create")
     */
    public function create(Request $request, EvaluationService $evaluationService, EntityManagerInterface $manager, HttpSession $httpSession): Response
    {
        $evaluation = new Evaluation();
        $form = $this->createForm(EvaluationType::class, $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($evaluationService->hydrate($evaluation));
            $manager->flush();

            $httpSession->getFlashBag()->add('success', 'Soutenance ajoutée avec succès !');

            return $this->redirectToRoute('app_evaluation_index');
        }

        return $this->render('evaluation/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/update/{id}")
     */
    public function update(Request $request, EvaluationService $evaluationService, EntityManagerInterface $manager, HttpSession $httpSession, Evaluation $evaluation): Response
    {
        $this->denyAccessUnlessGranted(AppVoter::UPDATE, $evaluation);

        $form = $this->createForm(EvaluationType::class, $evaluation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $evaluationService->hydrate($evaluation);

            $manager->flush();

            $httpSession->getFlashBag()->add('success', 'Soutenance modifiée avec succès !');

            return $this->redirectToRoute('app_evaluation_index');
        }

        return $this->render('evaluation/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function delete(EvaluationService $evaluationService, HttpSession $httpSession, Evaluation $evaluation): RedirectResponse
    {
        $this->denyAccessUnlessGranted(AppVoter::DELETE, $evaluation);

        $evaluationService->delete($evaluation);

        $this->getDoctrine()->getManager()->flush();

        $httpSession->getFlashBag()->add('success', 'Soutenance supprimée avec succès !');

        return $this->redirectToRoute('app_evaluation_index');
    }
}
