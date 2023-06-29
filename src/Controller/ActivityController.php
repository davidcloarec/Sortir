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
use Symfony\Component\Validator\Constraints\Date;
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

    #[Route('/{id}/subscribe', name: 'subscribe', requirements: ["id" => "\d+"])]
    public function subscribe(
        $id,
        ActivityRepository $activityRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    ):Response
    {
        $activity = $activityRepository->find($id);
        if($activity->getState()->getLibelle() != "ouverte"){
            $this->addFlash('danger','Impossible de s\'inscrire');
        }
        else{
            if($activity->getParticipants()->contains($this->getUser()->getParticipant())){
                $this->addFlash('warning','déjà inscrit !');
            }
            else {
                $activity->addParticipant($this->getUser()->getParticipant());
                if(count($activity->getParticipants())>=$activity->getMaxSignUp()){
                    $activity->setState($stateRepository->findOneByLibelle("cloturé"));
                }
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success','Inscription réussie !');
            }
        }
        return $this->redirectToRoute('activity_list');
    }

    #[Route('/{id}/unsubscribe', name: 'unsubscribe', requirements: ["id" => "\d+"])]
    public function unsubscribe(
        $id,
        ActivityRepository $activityRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    ):Response
    {
        $activity = $activityRepository->find($id);
        $currentState = $activity->getState()->getLibelle();
        $participant = $this->getUser()->getParticipant();

        if($currentState == "ouverte" || $currentState == "cloturé"){
            if($activity->getParticipants()->contains($participant)){
                $activity->removeParticipant($participant);
                $participant->removeActivity($activity);
                if((new \DateTime()) < $activity->getSignUpLimit()) {
                    $activity->setState($stateRepository->findOneByLibelle("ouverte"));
                }
                $entityManager->persist($activity);
                $entityManager->persist($participant);
                $entityManager->flush();
                $this->addFlash('success', 'Désinscription réussie !');
            }
            else $this->addFlash('warning','Vous  n\'êtes pas inscrit !');
        }
        else $this->addFlash('danger','Erreur, impossible de se désinscrire');

        return $this->redirectToRoute('activity_list');
    }

    #[Route('/{id}/publish', name: 'publish', requirements: ["id" => "\d+"])]
    public function publish(
        $id,
        ActivityRepository $activityRepository,
        StateRepository $stateRepository,
        EntityManagerInterface $entityManager
    ):Response
    {
        $activity = $activityRepository->find($id);
        if($activity->getOrganizer() == $this->getUser()->getParticipant()
            && $activity->getState()->getLibelle() == 'créée')
        {
            if((new \DateTime()) > $activity->getSignUpLimit()){
                $this->addFlash('warning','impossible de creer la sortie, la date de cloture est déjà passé');
            }
            elseif((new \DateTime()) > $activity->getStartingTime()){
                $this->addFlash('warning','impossible de creer la sortie, la date de sortie est déjà passé');
            }
            else{
                $activity->setState($stateRepository->findOneByLibelle("ouverte"));
                $entityManager->persist($activity);
                $entityManager->flush();
                $this->addFlash('success','la sortie à été publiée !');
            }
        }
        else $this->addFlash('danger', 'vous ne pouvez pas publier cette sortie');

        return $this->redirectToRoute('activity_list');
    }
}
