<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PlumberDashboardController extends AbstractController
{
    #[Route('/plumber/dashboard', name: 'app_plumber_dashboard')]
    public function index(): Response
    {
        return $this->render('plumber_dashboard/index.html.twig', [
            'controller_name' => 'PlumberDashboardController',
        ]);
    }
}
