<?php

namespace App\Controller;

use App\Entity\Availibilities;
use App\Form\AvailibilitiesType;
use App\Repository\AvailibilitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/availibilities')]
final class AvailibilitiesController extends AbstractController
{
    #[Route(name: 'app_availibilities_index', methods: ['GET'])]
    public function index(AvailibilitiesRepository $availibilitiesRepository): Response
    {
        return $this->render('availibilities/index.html.twig', [
            'availibilities' => $availibilitiesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_availibilities_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $availibility = new Availibilities();
        $form = $this->createForm(AvailibilitiesType::class, $availibility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($availibility);
            $entityManager->flush();

            return $this->redirectToRoute('app_availibilities_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availibilities/new.html.twig', [
            'availibility' => $availibility,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availibilities_show', methods: ['GET'])]
    public function show(Availibilities $availibility): Response
    {
        return $this->render('availibilities/show.html.twig', [
            'availibility' => $availibility,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_availibilities_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Availibilities $availibility, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvailibilitiesType::class, $availibility);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_availibilities_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('availibilities/edit.html.twig', [
            'availibility' => $availibility,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_availibilities_delete', methods: ['POST'])]
    public function delete(Request $request, Availibilities $availibility, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$availibility->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($availibility);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_availibilities_index', [], Response::HTTP_SEE_OTHER);
    }
}
