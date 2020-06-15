<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Path;
use App\Form\PathType;
use App\Repository\PathRepository;
use App\Service\OCApiClient;
use App\Service\PathService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/paths")
 *
 * Class PathController
 */
class PathController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(PathRepository $pathRepository): Response
    {
        return $this->render('path/index.html.twig', ['paths' => $pathRepository->findAll()]);
    }

    /**
     * @Route("/create")
     */
    public function create(Request $request, OCApiClient $client, EntityManagerInterface $manager, PathService $pathService, HttpSession $httpSession): Response
    {
        $path = new Path();
        $form = $this->createForm(PathType::class, $path);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (empty($pathData = $client->getPath($form->get('idOC')->getData()))) {
                $httpSession->getFlashBag()->add('danger', 'Parcours introuvable !');

                return $this->redirectToRoute('app_path_index');
            }

            $manager->persist($pathService->createOrUpdate($pathData));
            $manager->flush();

            $flashBag = $httpSession->getFlashBag();
            $flashBag->add('success', 'Parcours ajouté avec succès !');
            $flashBag->add('warning', 'Merci de vérifier que les niveaux (taux) des projets sont corrects');

            return $this->redirectToRoute('app_path_read', ['idOC' => $path->getIdOC()]);
        }

        return $this->render('path/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/read/{idOC}")
     */
    public function read(Path $path): Response
    {
        return $this->render('path/read.html.twig', ['path' => $path]);
    }

    /**
     * @Route("/delete/{idOC}")
     */
    public function delete(HttpSession $httpSession, PathService $pathService, Path $path): RedirectResponse
    {
        $pathService->delete($path);

        $this->getDoctrine()->getManager()->flush();

        $httpSession->getFlashBag()->add('success', 'Parcours supprimé avec succès !');

        return $this->redirectToRoute('app_path_index');
    }
}
