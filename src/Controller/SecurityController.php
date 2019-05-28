<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\LoginFormAuthenticator;
use Doctrine\Common\Persistence\ObjectManager;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends AbstractController
{

    /**
     * Registration page.
     * @Route("/register", name="app_register")
     *
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param LoginFormAuthenticator $authenticator
     * @param GuardAuthenticatorHandler $guardHandler
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function register(
        Request $request,
        ObjectManager $manager,
        UserPasswordEncoderInterface $passwordEncoder,
        LoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Swift_Mailer $mailer
    ): Response {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);

            $user->setRoles(['ROLE_USER'])
                ->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));

            $manager->flush();

            $this->addFlash('success', 'Bienvenue parmi nous ' . $user->getPseudo() . ' !');
            $this->addFlash('info', $this->renderView('security/registration/registration_sucess.html.twig'));

            $message = (new Swift_Message('Bienvenue sur Werewolves !'))
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        ['user' => $user]
                    ),
                    'text/html');

            $mailer->send($message);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main'
            );
        }

        return $this->render('security/registration/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Login page.
     * @Route("/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * Logout page.
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }
}
