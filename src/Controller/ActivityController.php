<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity', name: 'activity_')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(
        ActivityRepository $activityRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        $activities = $activityRepository->findAll();
        $participants = $participantRepository->findAll();
        $activitiesCount = $activityRepository->count([]);

        return $this->render('activity/list.html.twig', [
            'activities' => $activities,
            'participants' => $participants,
            'activitiesCount' => $activitiesCount
        ]);
    }

    /**
     * @param $id
     * @return Response
     */
    #[Route(
        '/{id}',
        name: 'details',
        requirements: ["id" => "\d+"],
        methods: ["GET"]
    )]
    public function details($id,
                            ActivityRepository $activityRepository,
                            ParticipantRepository $participantRepository
    ): Response
    {
        $activity = $activityRepository->find($id);
        $participant = $participantRepository->find($id);

        return $this->render('activity/details.html.twig', [
            "activity" => $activity,
            'participant' => $participant
        ]);
    }





}
