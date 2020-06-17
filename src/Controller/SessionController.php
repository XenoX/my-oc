<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Session;
use App\Entity\Student;
use App\Form\SessionType;
use App\Repository\SessionRepository;
use App\Security\Voter\AppVoter;
use App\Service\SessionService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sessions")
 */
class SessionController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(SessionInterface $session, SessionRepository $sessionRepository): Response
    {
        return $this->render('session/index.html.twig', [
            'sessions' => $sessionRepository->findByMonth(
                $session->get('yearAndMonth', (new DateTime())->format('Y-m')),
                $this->getUser()
            ),
        ]);
    }

    /**
     * @Route("/create")
     */
    public function create(Request $request, SessionService $sessionService, EntityManagerInterface $manager, HttpSession $httpSession): Response
    {
        $session = new Session();
        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($sessionService->hydrate($session));
            $manager->flush();

            $httpSession->getFlashBag()->add('success', 'Session ajoutée avec succès !');

            return $this->redirectToRoute('app_session_index');
        }

        return $this->render('session/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/update/{id}")
     */
    public function update(Request $request, SessionService $sessionService, EntityManagerInterface $manager, HttpSession $httpSession, Session $session): Response
    {
        $this->denyAccessUnlessGranted(AppVoter::UPDATE, $session);

        $form = $this->createForm(SessionType::class, $session);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sessionService->hydrate($session);

            $manager->flush();

            $httpSession->getFlashBag()->add('success', 'Session modifiée avec succès !');

            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('session/update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function delete(Request $request, SessionService $sessionService, HttpSession $httpSession, Session $session): RedirectResponse
    {
        $this->denyAccessUnlessGranted(AppVoter::DELETE, $session);

        $sessionService->delete($session);

        $this->getDoctrine()->getManager()->flush();

        $httpSession->getFlashBag()->add('success', 'Session supprimée avec succès !');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/evaluation/add/{id}")
     */
    public function addEvaluation(SessionService $sessionService, Student $student): RedirectResponse
    {
        $sessionService->createSessionForStudent($student, true);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_app_index');
    }

    /**
     * @Route("/add/{id}/{noShow}", defaults={"noShow": 0}, requirements={"noShow": "1|0"})
     */
    public function addSession(SessionService $sessionService, Student $student, int $noShow): RedirectResponse
    {
        $sessionService->createSessionForStudent($student, false, (bool) $noShow);

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_app_index');
    }
}
