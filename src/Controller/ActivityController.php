<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Repository\ActivityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity', name: 'activity_')]
class ActivityController extends AbstractController
{
    #[Route('/', name: 'list')]
    public function list(ActivityRepository $activityRepository): Response
    {
        $activities = $activityRepository->findAll();
        $activitiesCount = $activityRepository->count([]);

        return $this->render('activity/list.html.twig', [
            'activities' => $activities,
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
    public function details($id, ActivityRepository $activityRepository): Response
    {
        $activity = $activityRepository->find($id);

        return $this->render('activity/details.html.twig', [
            "activity" => $activity
        ]);
    }





}
