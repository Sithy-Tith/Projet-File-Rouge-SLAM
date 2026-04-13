<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard')]
    public function index(): Response
    {
        return $this->render('client_dashboard/index.html.twig', [
            'controller_name' => 'ClientDashboardController',
        ]);
    }
}
