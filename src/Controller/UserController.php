<?php

namespace App\Controller;

use App\Entity\Evaluation;
use App\Entity\Session;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/profile")
     */
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, HttpSession $httpSession): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (($oldPassword = $form->get('oldPassword')->getData())
                && ($newPassword = $form->get('newPassword')->getData())
            ) {
                if (!$passwordEncoder->isPasswordValid($user, $oldPassword)) {
                    $httpSession->getFlashBag()->add('danger', 'Ancien mot de passe invalide !');

                    return $this->redirectToRoute('app_user_profile');
                }

                $user->setPassword($passwordEncoder->encodePassword($newPassword, null));
            }

            $entityManager->flush();

            $httpSession->getFlashBag()->add('success', 'Informations modifiées avec succès !');

            return $this->redirectToRoute('app_user_profile');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
            'sessionsCount' => $entityManager->getRepository(Session::class)->count(['user' => $user]),
            'evaluationsCount' => $entityManager->getRepository(Evaluation::class)->count(['user' => $user]),
        ]);
    }

    /**
     * @Route("/delete")
     */
    public function delete(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        foreach ($entityManager->getRepository(Evaluation::class)->findBy(['user' => $user]) as $evaluation) {
            $entityManager->remove($evaluation);
        }

        $tokenStorage->setToken(null);

        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_security_logout');
    }
}
