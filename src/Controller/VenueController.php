<?php

namespace App\Controller;

use App\Entity\Venue;
use App\Form\VenueType;
use App\Repository\VenueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/venue',name:'app_venue_')]
class VenueController extends AbstractController
{
    #[Route('/admin/', name: 'index')]
    public function index(VenueRepository $venueRepository): Response
    {
        $venues = $venueRepository->findAll();
        return $this->render('venue/index.html.twig', [
            'controller_name' => 'VenueController',
            'venues'=>$venues,
        ]);
    }
    #[Route('/new', name: 'new')]
    public function newVenue(Request $request,EntityManagerInterface $entityManager): Response
    {
        $venue = new Venue();
        $form = $this->createForm(VenueType::class,$venue);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $city = $form->get('city')->getData();
            $venue->setCity($city);
            $entityManager->persist($venue);
            $entityManager->flush();

            return $this->redirectToRoute('activity_create');
        }
        return $this->render('venue/new.html.twig', [
            'controller_name' => 'VenueController',
            'form'=>$form,
        ]);
    }
    #[Route('/admin/{id}/edit', name: 'edit')]
    public function editVenue(Request $request,EntityManagerInterface $entityManager,VenueRepository $venueRepository,$id): Response
    {
        $venue = $venueRepository->find($id);
        $form = $this->createForm(VenueType::class,$venue);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $venue->setCity($form->get('city')->getData());
            $entityManager->persist($venue);
            $entityManager->flush();

            return $this->redirectToRoute('app_venue_index');
        }
        return $this->render('venue/edit.html.twig', [
            'controller_name' => 'VenueController',
            'form'=>$form,
            'venue'=>$venue,
        ]);
    }
    #[Route('/admin/{id}/delete',name:'delete')]
    public function deleteVenue(VenueRepository $venueRepository,$id): RedirectResponse
    {
        $venue = $venueRepository->find($id);
        if($venue){
            $venueRepository->remove($venue,true);
        }
        return $this->redirectToRoute('app_venue_index');
    }
}