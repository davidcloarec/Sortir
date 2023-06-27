<?php

namespace App\Controller;

use App\classe\Search;
use App\Entity\Activity;
use App\Entity\Participant;
use App\Entity\State;
use App\Form\ActivityType;
use App\Form\SearchType;
use App\Repository\ActivityRepository;
use App\Repository\CampusRepository;
use App\Repository\CityRepository;
use App\Repository\ParticipantRepository;
use App\Repository\StateRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\equalTo;

#[Route('/activity', name:'activity_')]
class ActivityController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(
        EntityManagerInterface $entityManager,
        StateRepository $stateRepository,
        CityRepository $cityRepository,
        Request $request): Response
    {
        $activity = new Activity();
        $cities = $cityRepository->findAll();

        //recuperation des infos en session
        $user = $this->getUser();
        $organizer = $user->getParticipant();
        $activity->setOrganizer($organizer);

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid()){
            $button = $request->get("button");
            if($button == "save") $state = $stateRepository->findOneByLibelle("créée");
            else $state = $stateRepository->findOneByLibelle("ouverte");
            $activity->setState($state);

            $entityManager->persist($activity);
            $entityManager->flush();
            return $this->redirectToRoute('activity_list');
        }

        return $this->render('activity/create.html.twig', [
            'activityForm' => $activityForm->createView(),
            'cities'=> $cities
        ]);
    }

    #[Route('/', name: 'list')]
    public function list(
        Request $request,
        EntityManagerInterface $entityManager,
        ActivityRepository $activityRepository,
        CampusRepository $campusRepository,
        ParticipantRepository $participantRepository
    ): Response
    {
        /******SearchFilterStart******/
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $activities = $entityManager->getRepository(Activity::class)->findWithSearch($search);
//            $activities = $activityRepository->findWithSearch($search);
        } else {
            $activities = $entityManager->getRepository(Activity::class)->findAll();
//            $activities = $activityRepository->findAll();
        }
        /******SearchFilterEnd******/

//        $activities = $activityRepository->findAll(); // a decommenter si searchfiltr viré
        $participants = $participantRepository->findAll();
        $activitiesCount = $activityRepository->count([]);
//        $campus = $campusRepository->findAll();

        return $this->render('activity/list.html.twig', [
//            'campus' => $campus,
            'activities' => $activities,
            'participants' => $participants,
            'activitiesCount' => $activitiesCount,
            'form' =>$form->createView()
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
                            ParticipantRepository $participantRepository,
                            UserRepository $userRepository
    ): Response
    {
        $activity = $activityRepository->find($id);
        $participant = $participantRepository->find($id);
        $user = $userRepository->find($id);

        return $this->render('activity/details.html.twig', [
            "activity" => $activity,
            'participant' => $participant,
            'user' => $user
        ]);
    }
}
