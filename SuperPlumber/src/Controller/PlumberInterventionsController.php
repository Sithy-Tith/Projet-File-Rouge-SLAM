<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Form\InterventionsPlumberType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/plumber')]
final class PlumberInterventionsController extends AbstractController
{
    #[Route('/interventions', name: 'app_plumber_interventions')]
    public function index(EntityManagerInterface $em): Response
    {
        $myInterventions = $em->getRepository(Interventions::class)->findBy(
            ['fkEmployee' => $this->getUser()],
            ['startAt' => 'ASC'],
        );
        return $this->render('plumber_interventions/index.html.twig', [
            // 'controller_name' => 'PlumberInterventionsController', (on garde ça peut être utile)
            'interventions' => $myInterventions,
        ]);
    }

    #[Route('/interventions/{id}', name: 'app_plumber_intervention_show', methods: ['GET'])]
    public function show_intervention(Interventions $intervention): Response
    {
        if ($this->getUser() === $intervention->getFkEmployee()) {
            return $this->render('plumber_interventions/intervention_show.html.twig', [
                'intervention' => $intervention,
            ]);
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }

    #[Route('/interventions/{id}/edit', name: 'app_plumber_intervention_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Interventions $intervention, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $intervention->getFkEmployee()) {
            $form = $this->createForm(InterventionsPlumberType::class, $intervention);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Intervention modifiée.');

                return $this->redirectToRoute('app_plumber_intervention_show',  ['id' => $intervention->getId()], Response::HTTP_SEE_OTHER);
            }

            return $this->render('plumber_interventions/edit.html.twig', [
                'intervention' => $intervention,
                'form' => $form,
            ]);
        }

        throw new AccessDeniedHttpException('Accès refusé');
    }
}
