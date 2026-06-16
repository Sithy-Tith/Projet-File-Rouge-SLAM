<?php

namespace App\Controller;

use App\Entity\Availabilities;
use App\Form\AvailabilitiesType;
use App\Repository\AvailabilitiesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

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
                ['start' => 'ASC']
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
        $availability->setStart((new \DateTime())->setTime(8, 0)); // Par défaut : aujourd'hui à 8h
        $availability->setEnd((new \DateTime())->setTime(16, 0)); // Par défaut : aujourd'hui à 16h
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

    //Fonction pour mettre à jour les dates d'une disponibilité via une requête AJAX (utilisée par FullCalendar)
    #[Route('/{id}/update-dates', name: 'app_availabilities_update_dates', methods: ['POST'])]
    public function updateDates(Request $request, Availabilities $availability, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN') && $availability->getFkEmployee() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('update_availability', $token))) {
            return new JsonResponse(['error' => 'Jeton CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        $data = json_decode($request->getContent(), true);
        $timezone = new \DateTimeZone('Europe/Paris');
        $start = null;
        $end = null;

        if (isset($data['start'])) {
            $start = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['start'], $timezone);
            if (!$start) {
                $start = new \DateTime($data['start'], $timezone);
            }
        }

        if (isset($data['end'])) {
            $end = \DateTime::createFromFormat('Y-m-d\TH:i:s', $data['end'], $timezone);
            if (!$end) {
                $end = new \DateTime($data['end'], $timezone);
            }
        }

        if (!$start || !$end) {
            return new JsonResponse(['error' => 'Dates invalides.'], Response::HTTP_BAD_REQUEST);
        }

        $availability->setStart($start);
        $availability->setEnd($end);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/{id}', name: 'app_availabilities_delete', methods: ['POST'])]
    public function delete(Request $request, Availabilities $availability, EntityManagerInterface $entityManager): Response
    {
        // seulement les admins peuvent supprimer
        if (!$this->isGranted('ROLE_ADMIN') && $availability->getFkEmployee() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete' . $availability->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($availability);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_availabilities_index', [], Response::HTTP_SEE_OTHER);
    }
}
