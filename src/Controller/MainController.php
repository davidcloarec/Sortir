<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MainController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/main', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/list.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}