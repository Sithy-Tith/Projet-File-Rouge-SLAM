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

#[Route('/profile)]
final class ProfileController extends AbstractController
{
    #[Route('/employee', name: 'app_employees_profile')]
    public function profile(Security $security): Response
    {
        $employee = $security->getUser();
        return $this->render('employees/show.html.twig', [
            'employee' => $employee,
        ]);
    }
  }
