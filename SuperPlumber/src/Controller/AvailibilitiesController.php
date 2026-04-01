<?php

namespace App\Controller;

use App\Entity\availabilities;
use App\Form\availabilitiesType;
use App\Repository\availabilitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/availabilities')]
final class availabilitiesController extends AbstractController
{
    #[Route(name: 'app_availabilities_index', methods: ['GET'])]
    public function index(availabilitiesRepository $availabilitiesRepository): Response
    {
        return $this->render('availabilities/index.html.twig', [
            'availabilities' => $availabilitiesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_availabilities_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $availability = new availabilities();
        $form = $this->createForm(availabilitiesType::class, $availability);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($availability);
            $entityManager->flush();

            return $this->redirectToRoute('app_availabilities_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availabilities/new.html.twig', [
            'availability' => $availability,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availabilities_show', methods: ['GET'])]
    public function show(availabilities $availability): Response
    {
        return $this->render('availabilities/show.html.twig', [
            'availability' => $availability,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_availabilities_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, availabilities $availability, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(availabilitiesType::class, $availability);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_availabilities_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availabilities/edit.html.twig', [
            'availability' => $availability,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availabilities_delete', methods: ['POST'])]
    public function delete(Request $request, availabilities $availability, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$availability->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($availability);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_availabilities_index', [], Response::HTTP_SEE_OTHER);
    }
}
