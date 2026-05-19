<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlumberInterventionsController extends AbstractController
{
    #[Route('/plumber/interventions', name: 'app_plumber_interventions')]
    public function index(): Response
    {
        return $this->render('plumber_interventions/index.html.twig', [
            'controller_name' => 'PlumberInterventionsController',
        ]);
    }
}
