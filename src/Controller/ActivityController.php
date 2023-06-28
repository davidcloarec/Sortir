<?php

namespace App\Controller;

use App\classe\Search;
use App\Entity\Activity;
use App\Entity\Campus;
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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            $this->addFlash("success", "Sortie créée");
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
//            dd($search);
            $campus = $form->get("campus")->getData();
            $search->campus=$campus;

            $activities = $activityRepository->findWithSearch($search);
        } else {
            $activities = $activityRepository->findAll();

        }

        /******SearchFilterEnd******/

        $participants = $participantRepository->findAll();
        $activitiesCount = $activityRepository->count([]);


        return $this->render('activity/list.html.twig', [

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

    #[Route('/{id}/update', name: 'update', requirements: ["id" => "\d+"])]
    public function update($id,
        EntityManagerInterface $entityManager,
        ActivityRepository $activityRepository,
        StateRepository $stateRepository,
        CityRepository $cityRepository,
        Request $request): Response
    {
        $activity = $activityRepository->find($id);
        $cities = $cityRepository->findAll();

        //verif droits
        if($activity->getState()->getLibelle() != "créée"){
            return $this->redirectToRoute('activity_list');
        }

        $activityForm = $this->createForm(ActivityType::class, $activity);
        $activityForm->handleRequest($request);

        if($activityForm->isSubmitted() && $activityForm->isValid()){
            $button = $request->get("button");
            if($button == "delete"){
                $entityManager->remove($activity);
                $this->addFlash("success", "Sortie supprimée");
            }
            else {
                if($button == "save") $state = $stateRepository->findOneByLibelle("créée");
                else $state = $stateRepository->findOneByLibelle("ouverte");
                $activity->setState($state);
                $entityManager->persist($activity);
                $this->addFlash("success", "Sortie modifiée");
            }
            $entityManager->flush();
            return $this->redirectToRoute('activity_list');
        }

        return $this->render('activity/update.html.twig', [
            'activityForm' => $activityForm->createView(),
            'activity'=>$activity,
            'cities'=> $cities
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(
        $id,
        EntityManagerInterface $entityManager,
        ActivityRepository $activityRepository,
        StateRepository $stateRepository,
        Request $request): Response
    {
        $activity = $activityRepository->find($id);

        //verif droits
        if($activity->getState()->getLibelle() != "ouverte"){
            return $this->redirectToRoute('activity_list');
        }

        if($request->get('cancelMotive') != null){
            $state = $stateRepository->findOneByLibelle("annulée");
            $activity->setCancelMotive($request->get('cancelMotive'));
            $activity->setState($state);
            $entityManager->persist($activity);
            $entityManager->flush();
            $this->addFlash("success", "Sortie annulée");
            return $this->redirectToRoute('activity_list');
        }

        return $this->render('activity/delete.html.twig', [
            'activity'=> $activity
        ]);

    }
}
