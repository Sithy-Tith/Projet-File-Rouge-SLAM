<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Form\InterventionsType;
use App\Repository\InterventionsRepository;
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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $intervention = new Interventions();
        $form = $this->createForm(InterventionsType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($intervention);
            $entityManager->flush();

            return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('interventions/new.html.twig', [
            'intervention' => $intervention,
            'form' => $form,
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
}
