<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/profil/client', name: 'app_clients_profile')]
    #[IsGranted('ROLE_CLIENT')]
    public function client(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('clients/show.html.twig', [
            'client' => $user,
        ]);
    }

    #[Route('/profil/employee', name: 'app_employees_profile')]
    #[IsGranted('ROLE_EMPLOYEE')]
    public function employee(Security $security): Response
    {
        $user = $security->getUser();
        return $this->render('employees/show.html.twig', [
            'employee' => $user,
        ]);
    }
}
