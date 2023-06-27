<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Participant;
use App\Entity\State;
use App\Form\ActivityType;
use App\Repository\CampusRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity', name:'activity_')]
class ActivityController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        StateRepository $stateRepository,
        ParticipantRepository $participantRepository,
        CampusRepository $campusRepository,
        Request $request): Response
    {
        $activity = new Activity();
        $activityForm = $this->createForm(ActivityType::class, $activity);

        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid()){

            $state = $stateRepository->find(1);
            $organizer = $participantRepository->find(1);
            $campus = $campusRepository->find(1);

            $activity->setState($state);
            $activity->setOrganizer($organizer);
            $activity->setCampus($campus);

            $entityManager->persist($activity);
            $entityManager->flush();
        }

        return $this->render('activity/create.html.twig', [
            'activityForm' => $activityForm->createView()
        ]);
    }
}
