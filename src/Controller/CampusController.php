<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route("/admin/campus")]
class CampusController extends AbstractController
{
    #[Route('/', name: 'app_campus_index')]
    public function index(CampusRepository $campusRepository): Response
    {
        $campuses = $campusRepository->findAll();
        return $this->render('campus/index.html.twig', [
            'controller_name' => 'CampusController',
            'campuses' => $campuses,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_campus_edit')]
    public function editCampus(CampusRepository $campusRepository, EntityManagerInterface $entityManager, $id, Request $request): Response
    {
        $campus = $campusRepository->find($id);
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('campus/edit.html.twig', [
            'controller_name' => 'CampusController',
            'campus' => $campus,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_campus_new')]
    public function newCampus(EntityManagerInterface $entityManager, Request $request): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            return $this->redirectToRoute('app_campus_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('campus/new.html.twig', [
            'controller_name' => 'CampusController',
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_campus_delete')]
    public function deleteCampus(CampusRepository $campusRepository, $id)
    {
        $campus = $campusRepository->find($id);
        if ($campus) {
            $campusRepository->remove($campus, true);
        }
        return $this->redirectToRoute('app_campus_index');
    }
}