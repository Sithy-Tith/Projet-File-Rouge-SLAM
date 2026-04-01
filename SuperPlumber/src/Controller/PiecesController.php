<?php

namespace App\Controller;

use App\Entity\Pieces;
use App\Form\PiecesType;
use App\Repository\PiecesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pieces')]
final class PiecesController extends AbstractController
{
    #[Route(name: 'app_pieces_index', methods: ['GET'])]
    public function index(PiecesRepository $piecesRepository): Response
    {
        return $this->render('pieces/index.html.twig', [
            'pieces' => $piecesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pieces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $piece = new Pieces();
        $form = $this->createForm(PiecesType::class, $piece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($piece);
            $entityManager->flush();

            return $this->redirectToRoute('app_pieces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pieces/new.html.twig', [
            'piece' => $piece,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pieces_show', methods: ['GET'])]
    public function show(Pieces $piece): Response
    {
        return $this->render('pieces/show.html.twig', [
            'piece' => $piece,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pieces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pieces $piece, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PiecesType::class, $piece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pieces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pieces/edit.html.twig', [
            'piece' => $piece,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pieces_delete', methods: ['POST'])]
    public function delete(Request $request, Pieces $piece, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$piece->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($piece);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pieces_index', [], Response::HTTP_SEE_OTHER);
    }
}
