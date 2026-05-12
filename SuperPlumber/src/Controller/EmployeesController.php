<?php

namespace App\Controller;

use App\Entity\Employees;
use App\Form\EmployeesType;
use App\Repository\EmployeesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/employees')]
final class EmployeesController extends AbstractController
{
    #[Route(name: 'app_employees_index', methods: ['GET'])]
    public function index(EmployeesRepository $employeesRepository, Request $request): Response
    {
        // Récuperer le terme de la recheche dans l'url
        $search = $request->query->get('search');

        // Si du texte a été tapé dans la barre de recherche, on n'affiche que ceux correspondant à la recherche
        if ($search) {
            $employees = $employeesRepository->searchByTerm($search);
            $isSearch = true;
        } else {
            $employees = $employeesRepository->findAll();
        }


        return $this->render('employees/index.html.twig', [
            'employees' => $employees,
            'isSearch' => $isSearch ?? false,
            'term' => $search,
        ]);
    }

    #[Route('/new', name: 'app_employees_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $hasher): Response
    {
        $employee = new Employees();
        $form = $this->createForm(EmployeesType::class, $employee, ['is_new' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();
            $employee->setPassword($hasher->hashPassword($employee, $password));

            $entityManager->persist($employee);
            $entityManager->flush();

            return $this->redirectToRoute('app_employees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employees/new.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }


    #Page du profil d'un employé, accessible que par lui
    #[Route('/profil', name: 'app_employees_profile')]
    public function profile(Security $security): Response
    {
        $employee = $security->getUser();
        return $this->render('employees/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}', name: 'app_employees_show', methods: ['GET'])]
    public function show(Employees $employee): Response
    {
        return $this->render('employees/show.html.twig', [
            'employee' => $employee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employees_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employees $employee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeesType::class, $employee, ['is_new' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employees_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('employees/edit.html.twig', [
            'employee' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employees_delete', methods: ['POST'])]
    public function delete(Request $request, Employees $employee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $employee->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($employee);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employees_index', [], Response::HTTP_SEE_OTHER);
    }

    # Méthode permettant à l'admin d'avoir la liste des employés pour switch
    #[Route('/admin/impersonate', name: 'admin_impersonate')]
    public function impersonate(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $em->getRepository(Employees::class)->findAll();

        return $this->render('admin/impersonate.html.twig', [
            'users' => $users
        ]);
    }
}
