<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/oubli-pass', name: 'forgotten_password')]
    public  function  forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendMailService $mail
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // chercher l user par son mail
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            //verif si user present
            if($user) {
                //genere token reinit
                $token = $tokenGenerator->generateToken();
//                dd($token);

                $user->setResetToken($token);

                $entityManager->persist($user);
                $entityManager->flush();

                //lien de reinit mdp
                $url = $this->generateUrl(

                    'app_reset_pass',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL);

                //cree donnee mail
                $context = compact('url', 'user');

                //envoi mail
                $mail->send(
                    'no-reply@sortir.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',//teplate
                    $context
                );

                $this->addFlash('success', "Email envoyé avec succès");
                return  $this->redirectToRoute('app_login');

            }
            //$user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }
    #[Route(path: '/oubli-pass/{token}', name: 'app_reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response
    {
        //check si ce token en bddd
        $user = $userRepository->findOneByResetToken($token);
//        dd($user);
        if($user){
            $form = $this->createForm(ResetPasswordRequestFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                //efface token
                $user->setResetToken('');
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return  $this->redirectToRoute('app_login');
            }
            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'jeton invalide');
        return $this->redirectToRoute('app_login');

        return ;
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


}