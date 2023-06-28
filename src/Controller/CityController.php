<?php

namespace App\Controller;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CityController extends AbstractController
{
    #[Route('/', name: 'app_city_index')]
    public function index(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findAll();
        return $this->render('city/index.html.twig', [
            'controller_name' => 'CityController',
            'cities'=>$cities,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_city_edit')]
    public function editCampus(CityRepository $cityRepository,EntityManagerInterface $entityManager,$id,Request $request): Response
    {
        $city = $cityRepository->find($id);
        $form = $this->createForm(CityType::class,$city);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($city);
            $entityManager->flush();
            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('city/edit.html.twig', [
            'controller_name' => 'CityController',
            'city'=>$city,
            'form'=>$form,
        ]);
    }

    #[Route('/new', name: 'app_city_new')]
    public function newCampus(EntityManagerInterface $entityManager,Request $request): Response
    {
        $campus = new City();
        $form = $this->createForm(CityType::class,$campus);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('app_city_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('city/new.html.twig', [
            'controller_name' => 'CityController',
            'form'=>$form,
        ]);
    }
}