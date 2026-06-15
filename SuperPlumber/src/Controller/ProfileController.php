<?php

namespace App\Controller;

use App\Form\ClientsType;
use App\Form\EmployeesSelfType;
use App\Repository\ClientsRepository;
use App\Repository\EmployeesRepository;
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
        $form = $this->createForm(ClientsType::class, $client, ['is_new' => false,'from_client' => true]);
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


    #[Route('/employee/profile/edit', name: 'app_employees_profile_edit')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function edit_employee(
        Request $request,
        EmployeesRepository $employeesRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $hasher
    ): Response {

        $employee = $employeesRepository->findOneBy(['id' => $this->getUser()->getId()]);
        $form = $this->createForm(EmployeesSelfType::class, $employee, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData()) {
                $password = $form->get('password')->getData();
                $employee->setPassword($hasher->hashPassword($employee, $password));
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_employees_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employees/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }
}
