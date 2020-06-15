<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Path;
use App\Entity\Project;
use App\Entity\Student;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use App\Service\StudentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/students")
 *
 * Class StudentController
 */
class StudentController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(StudentRepository $studentRepository): Response
    {
        return $this->render('student/index.html.twig', ['students' => $studentRepository->findAll()]);
    }

    /**
     * @Route("/create")
     */
    public function create(Request $request, StudentService $studentService, EntityManagerInterface $em, Session $session): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $studentService->hydrate($student);

            $em->persist($student);
            $em->flush();

            $session->getFlashBag()->add('success', 'Mentoré·e ajouté·e avec succès !');

            return $this->redirectToRoute('app_student_index');
        }

        return $this->render('student/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/read/{id}")
     */
    public function read(SessionInterface $session, Student $student): Response
    {
        return $this->render('student/read.html.twig', [
            'sessions' => $student->getMonthSessions($session->get('yearAndMonth', (new \DateTime())->format('Y-m'))),
            'student' => $student,
        ]);
    }

    /**
     * @Route("/update/{id}")
     */
    public function update(Request $request, Session $session, Student $student): Response
    {
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $session->getFlashBag()->add('success', 'Mentoré·e modifié·e avec succès !');

            return $this->redirectToRoute('app_student_index');
        }

        return $this->render('student/update.html.twig', ['form' => $form->createView(), 'student' => $student]);
    }

    /**
     * @Route("/delete/{id}")
     */
    public function delete(StudentService $studentService, Session $session, Student $student): RedirectResponse
    {
        $studentService->delete($student);

        $this->getDoctrine()->getManager()->flush();

        $session->getFlashBag()->add('success', 'Mentoré·e supprimé·e avec succès !');

        return $this->redirectToRoute('app_student_index');
    }

    /**
     * @Route("/{id}/project/{idProject}/select")
     */
    public function selectProject(EntityManagerInterface $em, Session $session, Student $student, int $idProject): RedirectResponse
    {
        if (!$project = $em->getRepository(Project::class)->find($idProject)) {
            return $this->redirectToRoute('app_student_read', ['id' => $student->getId()]);
        }

        $student->setProject($project);

        $em->flush();

        $session->getFlashBag()->add('success', 'Changement de projet réalisé avec succès !');

        return $this->redirectToRoute('app_student_read', ['id' => $student->getId()]);
    }

    /**
     * @Route("/ajax/path-{id}/get-project-ids", options={"expose"=true})
     */
    public function getProjectIdsForPath(Path $path): JsonResponse
    {
        return new JsonResponse(
            array_map(static function ($project) {
                return (string) $project->getId();
            }, $path->getProjects()->toArray())
        );
    }
}
