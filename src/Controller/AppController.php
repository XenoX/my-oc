<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Path;
use App\Entity\Session;
use App\Entity\Student;
use App\Service\EarningService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController.
 */
class AppController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(SessionInterface $session, EntityManagerInterface $em, EarningService $earningService): Response
    {
        $yearAndMonth = $session->get('yearAndMonth', (new DateTime())->format('Y-m'));

        $sessionRepository = $em->getRepository(Session::class);
        $monthSessions = $sessionRepository->findByMonth($yearAndMonth);
        $monthEvaluations = $em->getRepository(Evaluation::class)->findByMonth($yearAndMonth);
        $expectedBonus = $earningService->getExpectedBonus();
        $studentsCount = $em->getRepository(Student::class)->count([]);

        return $this->render('app/index.html.twig', [
            'expectedMonthSessions' => $studentsCount * Session::SESSIONS_BY_MONTH - $sessionRepository->countEvalForMonth($yearAndMonth),
            'earnings' => $earningService->getEarnsForMeetings(array_merge($monthSessions, $monthEvaluations)) + $expectedBonus,
            'expectedEarnings' => $earningService->getExpectedEarnsForMonth($yearAndMonth) + $expectedBonus,
            'paths' => $em->getRepository(Path::class)->findAll(),
            'monthEvaluations' => $monthEvaluations,
            'monthSessions' => $monthSessions,
            'expectedBonus' => $expectedBonus,
            'studentsCount' => $studentsCount,
        ]);
    }

    /**
     * @Route("/ajax/month/update/{yearAndMonth}", options={"expose"=true})
     */
    public function month(SessionInterface $session, string $yearAndMonth): JsonResponse
    {
        $session->set('yearAndMonth', $yearAndMonth);

        return new JsonResponse([]);
    }
}
