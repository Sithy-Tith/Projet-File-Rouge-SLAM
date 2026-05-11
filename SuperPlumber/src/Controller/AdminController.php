<?php

namespace App\Controller;

use App\Entity\Employees;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{
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
