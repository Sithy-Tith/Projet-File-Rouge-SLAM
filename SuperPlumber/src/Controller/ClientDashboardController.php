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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

#[Route('/client')]
final class ClientDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_client_dashboard', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em, AvailabilitiesRepository $availRepo): Response
    {
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre_intervention');
            $description = $request->request->get('description');
            $dateChoisie = $request->request->get('date_souhaitee'); //date choisie par le client

            if (!empty($titre)) {
                // Mapping des valeurs du formulaire aux valeurs de l'enum Type
                $typeMapping = [
                    'fuite' => Type::FUITE,
                    'debouchage' => Type::DEBOUCHAGE,
                    'chauffe_eau' => Type::REPARATION,
                    'installation' => Type::INSTALLATION,
                    'autre' => Type::AUTRE,
                ];

                $intervention = new Interventions();
                $intervention->setType($typeMapping[$titre]);
                $intervention->setDescription($description);

                $intervention->setStatus(Status::TO_PLAN);
                $intervention->setfkClient($this->getUser());
                // Si le client a choisi une date, on la stocke comme startAt
                if ($dateChoisie) {
                    $intervention->setStartAt(new \DateTime($dateChoisie));
                }
                // sauvegarde doctrine
                $em->persist($intervention);
                $em->flush();

                $this->addFlash('success', 'Votre demande d\'intervention est en attente de validation.');

                return $this->redirectToRoute('app_client_dashboard');
            }
        }

        $mesInterventions = $em->getRepository(Interventions::class)->findBy(
            ['fkClient' => $this->getUser()],
            ['startAt' => 'ASC']
        );

        // Récupérer les dates distinctes des disponibilités futures
        $availabilities = $availRepo->findBy(
            [],
            ['start' => 'ASC']
        );

        return $this->render('clients/dashboard.html.twig', [
            'interventions' => $mesInterventions,
            'availabilities' => $availabilities,
        ]);
    }

    #[Route('/interventions', name: 'app_client_interventions', methods: ['GET', 'POST'])]
    public function list_interventions(Request $request, EntityManagerInterface $em): Response
    {
        $mesInterventions = $em->getRepository(Interventions::class)->findBy(
            ['fkClient' => $this->getUser()],
            ['startAt' => 'ASC']
        );

        return $this->render('clients/interventions_list.html.twig', [
            'interventions' => $mesInterventions
        ]);
    }


    #[Route('/interventions/{id}', name: 'app_client_intervention_show', methods: ['GET'])]
    public function show_intervention(Interventions $intervention): Response
    {
        if ($this->getUser() === $intervention->getFkClient()) {
            return $this->render('clients/intervention_show.html.twig', [
                'intervention' => $intervention,
            ]);
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    // Permet au client d'annuler une intervention prévue
    #[Route('/interventions/{id}/cancel', name: 'app_client_intervention_cancel', methods: ['GET'])]
    public function cancel_intervention(Interventions $intervention, EntityManagerInterface $em): Response
    {
        if ($this->getUser() === $intervention->getFkClient()) {
            $intervention->setStatus(Status::CANCELED);
            $em->persist($intervention);
            $em->flush();

            $this->addFlash('success', 'Votre intervention a été annulée');

            return $this->redirectToRoute('app_client_intervention_show', ['id' => $intervention->getId()], Response::HTTP_SEE_OTHER);
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }
}
