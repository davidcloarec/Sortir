<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Participant;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserParticipantCSVType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/register',name:'app_')]
class RegistrationController extends AbstractController
{

    #[Route('/', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
//            dd($form);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $participant = new Participant();
            $participant->setUser($user);
            $participant->setLastname($form->get('lastName')->getData());
            $participant->setFirstname($form->get('firstName')->getData());
            $participant->setMail($user->getEmail());
            $participant->setPhone($form->get('phone')->getData());
            $participant->setAdmin($form->get('admin')->getData());
            $participant->setActive($form->get('active')->getData());
            $campus = $form->get('campus')->getData();
            $participant->setCampus($campus);

            $entityManager->persist($participant);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('activity_list');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/csv', name: 'register_csv')]
    public function registerWithCSV(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager,CampusRepository $campusRepository): Response
    {
        $form = $this->createForm(UserParticipantCSVType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('submitFile')->getData();
            $handle = fopen($file->getPathname(), "r");
            $data = [];
            while (($csv = fgetcsv($handle)) !== false) {
                $data[] = str_getcsv($csv[0], ";");
            }
//            dd($data);

            foreach ($data as $csvUser) {
                $user = new User();
                $user->setEmail($csvUser[0]);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $csvUser[1]
                    )
                );
                $user->setUsername($csvUser[2]);
                $participant = new Participant();
                $participant->setUser($user);
                $participant->setLastname($csvUser[3]);
                $participant->setFirstname($csvUser[4]);
                $participant->setPhone($csvUser[5]);
                $participant->setMail($csvUser[6]);
                $participant->setAdmin($csvUser[7]);
                $participant->setActive($csvUser[8]);
                $campus = $campusRepository->findOneBy(["name"=>ucfirst($csvUser[9])]);
                if($campus){
                    $participant->setCampus($campus);
                }else{
                    $campus = new Campus();
                    $campus->setName(ucfirst($csvUser[9]));
                    $participant->setCampus($campus);
                }


                $entityManager->persist($campus);
                $entityManager->persist($participant);
                $entityManager->persist($user);
                $entityManager->flush();
            }


            return $this->redirectToRoute('activity_list');
        }
        return $this->render('registration/csv.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}