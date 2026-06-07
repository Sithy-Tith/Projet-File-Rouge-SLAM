<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Entity\Interventions;
use App\Form\ClientsType;
use App\Form\InterventionsType;
use App\Repository\ClientsRepository;
use App\Repository\InterventionsRepository;
use App\Repository\AvailabilitiesRepository;
use App\Enum\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/interventions')]
final class InterventionsController extends AbstractController
{
    #[Route(name: 'app_interventions_index', methods: ['GET'])]
    public function index(InterventionsRepository $interventionsRepository): Response
    {
        return $this->render('interventions/index.html.twig', [
            'interventions' => $interventionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_interventions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ClientsRepository $clientsRepository): Response
    {
        $intervention = new Interventions();
        $clientId = $request->query->get('client');
        // Si un client est passé dans l'URL
        if ($clientId) {
            $client = $clientsRepository->find($clientId);
            if ($client) {
                $intervention->setFkClient($client);
            }
        }

        $form = $this->createForm(InterventionsType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($intervention);
            $entityManager->flush();

            return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
        }

        $formIntervention = $this->createForm(InterventionsType::class, $intervention);
        $formClient = $this->createForm(ClientsType::class, new Clients(), [
            'origin' => 'intervention' // Permet de rediriger une création de client depuis intervention directement sur ce controller
        ]);


        return $this->render('interventions/new.html.twig', [
            'intervention' => $intervention,
            'formIntervention' => $formIntervention->createView(),
            'formClient' => $formClient->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_interventions_show', methods: ['GET'])]
    public function show(Interventions $intervention): Response
    {
        return $this->render('interventions/show.html.twig', [
            'intervention' => $intervention,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_interventions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Interventions $intervention, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InterventionsType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('interventions/edit.html.twig', [
            'intervention' => $intervention,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_interventions_delete', methods: ['POST'])]
    public function delete(Request $request, Interventions $intervention, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $intervention->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($intervention);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/assign', name: 'app_interventions_assign', methods: ['GET'])]
    public function assign(
        Interventions $intervention,
        Request $request,
        AvailabilitiesRepository $availRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $date = $request->query->get('date') ?? ($intervention->getStartAt()?->format('Y-m-d'));
        $duration = $request->query->get('duration');
        $availablePlumbers = [];

        if ($date && $duration) {
            $availablePlumbers = $availRepo->findAvailablePlumbers(
                new \DateTime($date),
                (int) $duration
            );
        }

        return $this->render('interventions/assign.html.twig', [
            'intervention' => $intervention,
            'availablePlumbers' => $availablePlumbers,
            'date' => $date,
            'duration' => $duration,
        ]);
    }

    #[Route('/{id}/assign', name: 'app_interventions_assign_confirm', methods: ['POST'])]
    public function assignConfirm(
        Interventions $intervention,
        Request $request,
        EntityManagerInterface $em,
        AvailabilitiesRepository $availRepo
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if (!$this->isCsrfTokenValid('assign' . $intervention->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $availabilityId = $request->request->get('availability_id');
        $duration = (int) $request->request->get('duration');
        $availability = $availRepo->find($availabilityId);

        $startAt = clone $availability->getStart();
        $endAt = (clone $startAt)->modify("+{$duration} minutes");

        $intervention->setFkEmployee($availability->getFkEmployee());
        $intervention->setFkAvailability($availability);
        $intervention->setStartAt($startAt);
        $intervention->setEndAt($endAt);
        $intervention->setStatus(Status::PLANNED);

        // Ajuster la dispo - enlever le créneau de l'intervention
        $availability->setStart($endAt);

        $em->flush();

        $this->addFlash('success', 'Intervention attribuée avec succès.');
        return $this->redirectToRoute('app_interventions_index');
    }
}
