<?php
namespace App\Controller;

use App\Entity\Interventions;
use App\Repository\AvailabilitiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class PlumberDashboardController extends AbstractController
{
    #[Route('/plumber/dashboard', name: 'app_plumber_dashboard')]
    public function index(EntityManagerInterface $em, AvailabilitiesRepository $availRepo): Response
    {
        $plumber = $this->getUser();

        // Toutes les interventions du plombier triées par date
        $interventions = $em->getRepository(Interventions::class)->findBy(
            ['fkEmployee' => $plumber],
            ['startAt' => 'ASC']
        );

        // Disponibilités du plombier
        $availabilities = $availRepo->findBy(
            ['fkEmployee' => $plumber],
            ['start' => 'ASC']
        );

        // Stats
        $today = new \DateTime('today');
        $todayEnd = new \DateTime('today 23:59:59');

        $todayCount = count(array_filter($interventions, fn($i) =>
            $i->getStartAt() >= $today && $i->getStartAt() <= $todayEnd
        ));

        $weekCount = count(array_filter($interventions, fn($i) =>
            $i->getStartAt() >= new \DateTime('monday this week') &&
            $i->getStartAt() <= new \DateTime('sunday this week 23:59:59')
        ));

        $ongoingCount = count(array_filter($interventions, fn($i) =>
            $i->getStatus()->value === 'ongoing'
        ));

        return $this->render('plumbers/dashboard.html.twig', [ // Retourne les variables de calcul stats vers la vue twig
            'interventions' => $interventions,
            'availabilities' => $availabilities,
            'todayCount' => $todayCount,
            'weekCount' => $weekCount,
            'ongoingCount' => $ongoingCount,
        ]);
    }
}
