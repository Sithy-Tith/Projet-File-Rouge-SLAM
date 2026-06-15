<?php

namespace App\Controller;

use App\Form\ClientsSelfType;
use App\Repository\ClientsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/client/profile', name: 'app_clients_profile')]
    #[IsGranted('ROLE_CLIENT')]
    public function client(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('clients/show.html.twig', [
            'client' => $user,
        ]);
    }

    #[Route('/client/profile/edit', name: 'app_clients_profile_edit')]
    #[IsGranted('ROLE_CLIENT')]
    public function edit_client(
        Request $request,
        ClientsRepository $clientsRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response {

        $client = $clientsRepository->findOneBy(['id' => $this->getUser()->getId()]);
        $form = $this->createForm(ClientsSelfType::class, $client, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData()) {
                $password = $form->get('password')->getData();
                $client->setPassword($hasher->hashPassword($client, $password));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_clients_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('clients/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }



    #[Route('/employee/profile', name: 'app_employees_profile')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function employee(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('employees/show.html.twig', [
            'employee' => $user,
        ]);
    }
}
