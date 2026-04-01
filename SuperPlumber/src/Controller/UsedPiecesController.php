<?php

namespace App\Controller;

use App\Entity\UsedPieces;
use App\Form\UsedPiecesType;
use App\Repository\UsedPiecesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/used/pieces')]
final class UsedPiecesController extends AbstractController
{
    #[Route(name: 'app_used_pieces_index', methods: ['GET'])]
    public function index(UsedPiecesRepository $usedPiecesRepository): Response
    {
        return $this->render('used_pieces/index.html.twig', [
            'used_pieces' => $usedPiecesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_used_pieces_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $usedPiece = new UsedPieces();
        $form = $this->createForm(UsedPiecesType::class, $usedPiece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($usedPiece);
            $entityManager->flush();

            return $this->redirectToRoute('app_used_pieces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('used_pieces/new.html.twig', [
            'used_piece' => $usedPiece,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_used_pieces_show', methods: ['GET'])]
    public function show(UsedPieces $usedPiece): Response
    {
        return $this->render('used_pieces/show.html.twig', [
            'used_piece' => $usedPiece,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_used_pieces_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UsedPieces $usedPiece, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UsedPiecesType::class, $usedPiece);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_used_pieces_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('used_pieces/edit.html.twig', [
            'used_piece' => $usedPiece,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_used_pieces_delete', methods: ['POST'])]
    public function delete(Request $request, UsedPieces $usedPiece, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$usedPiece->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($usedPiece);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_used_pieces_index', [], Response::HTTP_SEE_OTHER);
    }
}
