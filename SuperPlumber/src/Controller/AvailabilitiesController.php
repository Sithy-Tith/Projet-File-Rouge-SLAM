<?php

namespace App\Controller;

use App\Entity\Availabilities;
use App\Form\AvailabilitiesType;
use App\Repository\AvailabilitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/availabilities')]
final class AvailabilitiesController extends AbstractController
{
    #[Route(name: 'app_availabilities_index', methods: ['GET'])]
    public function index(AvailabilitiesRepository $availabilitiesRepository): Response
    {
        // si l'user actuel est un plombier, montre seulement leurs disponibilités
        if ($this->isGranted('ROLE_PLUMBER')) {
            $availabilities = $availabilitiesRepository->findBy(
                ['fkEmployee' => $this->getUser()],
                ['date' => 'ASC']
            );
        } else {
            $availabilities = $availabilitiesRepository->findAll();
        }

        return $this->render('availabilities/index.html.twig', [
            'availabilities' => $availabilities,
        ]);
    }

    #[Route('/new', name: 'app_availabilities_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $availability = new Availabilities();
        $form = $this->createForm(AvailabilitiesType::class, $availability);
        // si plombier, définir le fkEmployee sur l'utilisateur actuel et supprimer le champ fkEmployee du formulaire
        if ($this->isGranted('ROLE_PLUMBER')) {
            $availability->setFkEmployee($this->getUser());
            $form->remove('fkEmployee');
        }
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
    public function show(Availabilities $availability): Response
    {
        return $this->render('availabilities/show.html.twig', [
            'availability' => $availability,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_availabilities_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Availabilities $availability, EntityManagerInterface $entityManager): Response
    {
        // seulement les admins ou le propriétaire peuvent modifier
        if (!$this->isGranted('ROLE_ADMIN') && $availability->getFkEmployee() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(AvailabilitiesType::class, $availability);
        if ($this->isGranted('ROLE_PLUMBER')) {
            // empêche les plombiers de changer le fkEmployee
            $form->remove('fkEmployee');
        }
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
    public function delete(Request $request, Availabilities $availability, EntityManagerInterface $entityManager): Response
    {
        // seulement les admins peuvent supprimer
        if (!$this->isGranted('ROLE_ADMIN') && $availability->getFkEmployee() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$availability->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($availability);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_availabilities_index', [], Response::HTTP_SEE_OTHER);
    }
}
