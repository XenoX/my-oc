<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordFormType;
use App\Form\NewPasswordFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_app_index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/forgot-password")
     */
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        HttpSession $httpSession
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_app_index');
        }

        $form = $this->createForm(ForgotPasswordFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $to = $form->get('email')->getData();
            if ($user = $entityManager->getRepository(User::class)->findOneBy(['email' => $to])) {
                $mailer->send($this->getForgottenPasswordEmail($user->getToken(), $to));

                $httpSession->getFlashBag()->add(
                    'info',
                    'Un email avec un lien pour réinitialiser votre mot de passe vient de vous être envoyé, vous pouvez quitter la page'
                );

                return $this->redirectToRoute('app_security_login');
            }
        }

        return $this->render('security/forgot-password.html.twig', [
           'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("new-password/{token}")
     */
    public function newPassword(
        Request $request,
        UserRepository $userRepository,
        HttpSession $httpSession,
        UserPasswordEncoderInterface $encoder,
        string $token
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_app_index');
        }

        if (!$user = $userRepository->findOneBy(['token' => $token])) {
            $httpSession->getFlashBag()->add('danger', 'Token invalide, veuillez ressayer');

            return $this->redirectToRoute('app_security_forgotpassword');
        }

        $form = $this->createForm(NewPasswordFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->generateNewToken();

            $userRepository->upgradePassword(
                $user,
                $encoder->encodePassword($user, $form->get('newPassword')->getData())
            );

            $httpSession->getFlashBag()->add('success', 'Mot de passe modifié avec succès !');

            return $this->redirectToRoute('app_security_login');
        }

        return $this->render('security/new-password.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function getForgottenPasswordEmail(string $token, string $to): Email
    {
        $url = $this->generateUrl('app_security_newpassword', ['token' => $token], UrlGenerator::ABSOLUTE_URL);

        return (new Email())
            ->from('My OC <no-reply@my-oc.fr>')
            ->to($to)
            ->subject('Mot de passe oublié')
            ->text(sprintf('Vous venez de faire une demande pour changer de mot de passe, voici le lien : %s', $url))
            ->html(sprintf('<p>Vous venez de faire une demande pour changer de mot de passe, 
                voici le lien : <a href="%s">%s</a></p>', $url, $url)
            )
        ;
    }
}
