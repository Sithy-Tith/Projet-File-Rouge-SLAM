<?php

namespace App\Controller;

use App\Entity\Interventions;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PlumberInterventionsController extends AbstractController
{
    #[Route('/plumber/interventions', name: 'app_plumber_interventions')]
    public function index(EntityManagerInterface $em): Response
    {
        $myInterventions = $em->getRepository(Interventions::class)->findBy(
            ['fkEmployee' => $this->getUser()],
            ['startAt' => 'ASC'],
        );
        return $this->render('plumber_interventions/index.html.twig', [
            // 'controller_name' => 'PlumberInterventionsController', (on garde ça peut être utile)
            'interventions' => $myInterventions,
        ]);
    }
}
