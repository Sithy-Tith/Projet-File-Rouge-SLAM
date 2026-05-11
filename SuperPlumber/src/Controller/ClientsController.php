<?php

namespace App\Controller;

use App\Entity\Clients;
use App\Form\ClientsType;
use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/clients')]
final class ClientsController extends AbstractController
{
    #[Route(name: 'app_clients_index', methods: ['GET'])]
    public function index(ClientsRepository $clientsRepository, Request $request): Response
    {
        // Récuperer le terme de la recheche dans l'url
        $search = $request->query->get('search');

        // Si du texte a été tapé dans la barre de recherche, on n'affiche que ceux correspondant à la recherche
        if ($search) {
            $clients = $clientsRepository->searchByTerm($search);
            $isSearch = true;
        } else {
            $clients = $clientsRepository->findAll();
        }

        return $this->render('clients/index.html.twig', [
            'clients' => $clients,
            'isSearch' => $isSearch ?? false,
            'term' => $search,
        ]);
    }

    #[Route('/new', name: 'app_clients_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $client = new Clients();
        $form = $this->createForm(ClientsType::class, $client, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();
            $client->setPassword($hasher->hashPassword($client, $password));

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clients/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #Page du profil d'un client, accessible que par lui
    #[Route('/profil', name: 'app_clients_profile')]
    public function profile(Security $security): Response
    {
        $client = $security->getUser();
        return $this->render('clients/show.html.twig', [
            'client' => $client,
        ]);
    }


    #[Route('/{id}', name: 'app_clients_show', methods: ['GET'])]
    public function show(Clients $client): Response
    {
        return $this->render('clients/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_clients_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientsType::class, $client, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_clients_delete', methods: ['POST'])]
    public function delete(Request $request, Clients $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_clients_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/impersonate', name: 'admin_impersonate')]
    public function impersonate(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(Clients::class)->findAll();

        return $this->render('admin/impersonate.html.twig', [
            'users' => $users
        ]);
    }
}
