<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ImageRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/participant')]
class ParticipantController extends AbstractController
{
    #[Route('/admin/', name: 'app_participant_index', methods: ['GET'])]
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    #[Route('/admin/new', name: 'app_participant_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParticipantRepository $participantRepository): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantRepository->save($participant, true);

            return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_participant_show', methods: ['GET'])]
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_participant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participant $participant, UserPasswordHasherInterface $userPasswordHasher, ImageRepository $imageRepository, ParticipantRepository $participantRepository, UserRepository $userRepository, SluggerInterface $slugger, EntityManagerInterface $entityManager): Response
    {
        $loggedUser = $this->getUser();
        if (in_array('ROLE_ADMIN', $loggedUser->getRoles()) || $loggedUser->getParticipant()->getId() === $participant->getId()) {
            $form = $this->createForm(ParticipantType::class, $participant);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    if ($participant->getImage()) {
                        $image = $participant->getImage();
                    } else {
                        $image = new Image();
                        $participant->setImage($image);
                    }
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    // On viens nettoyer le nom du fichier pour éviter tout problème dans l'URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();


                    // On essaye de déplacer le fichier dans le dossier "profile_images"
                    try {
                        $profileImagesDirectory = $this->getParameter('profile_images');
                        $imageFile->move($profileImagesDirectory, $newFilename);
                    } catch (FileException $e) {
                        // ... On gère l'exception
                    }
                    // On enregistre le nom du fichier plutôt que le fichier lui même
                    $image->setImageFile($newFilename);
                    $participant->setImage($image);
                }

                $user = $participant->getUser();
                if($loggedUser->getId()===$participant->getUser()->getId()){
                    $plainPassword = $form->get("plainPassword")->getData();
                    if($plainPassword){
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                $plainPassword
                            )
                        );
                    }
                }

                $user->setEmail($participant->getMail());
                $user->setUsername($form->get("username")->getData());
                $entityManager->persist($user);
                $entityManager->persist($participant);
                $entityManager->flush();

                return $this->redirectToRoute('activity_list', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('participant/edit.html.twig', [
                'participant' => $participant,
                'form' => $form,
            ]);
        } else {
            $this->addFlash("danger", "Vous n'avez pas les droits.");
            return $this->redirectToRoute("activity_list");
        }
    }

    #[Route('/admin/{id}', name: 'app_participant_delete', methods: ['POST'])]
    public function delete(Request $request, Participant $participant, ParticipantRepository $participantRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $participant->getId(), $request->request->get('_token'))) {
            $participantRepository->remove($participant, true);
        }

        return $this->redirectToRoute('app_participant_index', [], Response::HTTP_SEE_OTHER);
    }
}