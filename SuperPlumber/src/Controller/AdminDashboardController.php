<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Enum\Status;
use App\Enum\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvailabilitiesRepository;
use App\Repository\InterventionsRepository;
use App\Repository\PiecesRepository;

#[Route('/admin')]
final class AdminDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin_dashboard', methods: ['GET', 'POST'])]
    public function index(
        PiecesRepository $piecesRepository,
        InterventionsRepository $interventionsRepository,
        AvailabilitiesRepository $availabilitiesRepository,
    ): Response {
        $alertPieces = $piecesRepository->getAlertPieces();
        $interToPlan = $interventionsRepository->findAllByStatus('to_plan');

        // Récupérer les dates distinctes des disponibilités futures
        $availabilities = $availabilitiesRepository->findBy(
            [],
            ['start' => 'ASC']
        );

        return $this->render('admin/dashboard.html.twig', [
            'alertPieces' => $alertPieces,
            'interToPlan' => $interToPlan,
            'availabilities' => $availabilities,
        ]);
    }
}
