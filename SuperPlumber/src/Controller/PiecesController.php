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
    public function index(): Response
    {
        return $this->redirectToRoute('app_pieces_inventory');
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

    #[Route('/{id}', name: 'app_pieces_show', methods: ['GET'], requirements: ['id' => '\d+'])]
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

    #[Route('/{id}', name: 'app_pieces_delete', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function delete(Request $request, Pieces $piece, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$piece->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($piece);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pieces_index', [], Response::HTTP_SEE_OTHER);
    }



//  --------   Méthodes personnalisées  ----------------

// Affiche l'inventaire de toutes les pièces avec leurs stock

#[Route('/inventaire' , name: 'app_pieces_inventory', methods: ['GET'])]
    public function inventory(PiecesRepository $piecesRepository, bool $edition= false): Response
    {
        $pieces = $piecesRepository->findAll();
        foreach($pieces as $piece){
            // Si la pièce possède un stock inférieur à son seuil d'alerte
            if ($piece->getQuantity()<= $piece->getAlertTreshold()){
                $alertPieces[]=$piece;
            }else{
                $normalPieces[]=$piece;
            }
        }
        return $this->render('pieces/index.html.twig', [
            'alertePieces' => $alertPieces,
            'normalPieces' => $normalPieces,
            'edition' => $edition
        ]);
    }





}
