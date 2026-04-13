<?php

namespace App\Controller;

use App\Entity\UsedPieces;
use App\Form\UsedPiecesType;
use App\Repository\UsedPiecesRepository;
use App\Repository\PiecesRepository;
use App\Repository\InterventionsRepository;
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


//  --------   Méthodes personnalisées  ----------------


// Afficher les pièces utilisées par l'intervention et un formulaire pour en utiliser d'autres
    #[Route('/{id}/use_piece', name: 'app_used_pieces_use_piece', methods: ['GET', 'POST'])]
    public function use_piece(
        int $id,    //id de l'intervention
        UsedPiecesRepository $usedPiecesRepository,
        PiecesRepository $piecesRepository,
        InterventionsRepository $interventionsRepository): Response
    {
        // On récupère l'intervention en cours
        $intervention=$interventionsRepository->findOneBy(['id' => $id]);
        // On récupère toutes les pièces du stock
        $pieces = $piecesRepository->findAll();
        // On récupère l'historique des pièces déjà utilisées par l'intervention
        $usedPieces = $usedPiecesRepository->findBy(['fkIntervention' => $id]);
        // On les affiche dans un formulaire personnalisé
        return $this->render('interventions/use_piece.html.twig', [
            'intervention' => $intervention,
            'pieces' => $pieces,
            'usedPieces' => $usedPieces,
        ]);
    }



// Après soumission du formulaire d'utilisation des pièces
// Update de la table UsedPieces et mise à jour du stock de la pièce si consommable
    #[Route('/{id}/use_piece_form_submitted', name: 'app_used_pieces_form_submitted', methods: ['POST'])]
    public function use_piece_submitted(
        int $id,   //id de l'intervention
        InterventionsRepository $interventionsRepository,    //Intervention actuelle, mapped by {id} dans la route
        PiecesRepository $piecesRepository, //Le repo des pieces pour pouvoir récupérer les infos d'une pièce
        EntityManagerInterface $entityManager)  //Manager pour pouvoir mettre à jour la BDD
    {
        // On récupère l'intervention en cours
        $intervention=$interventionsRepository->findOneBy(['id' => $id]);
        // Récupérer les données du formulaire
        $pieces = $_POST;

        $usedPieces= []; // Tableau pour stocker temporairement l'ensemble des UsedPieces du formulaire
        $lastId=null;
        // Pour chaque input du formulaire
        foreach($pieces as $key=>$value){
            // Chaque Input du form est de type "quantity_{idPiece}"
            // On vient récupérer l'attribut (quantity) et l'id avec explode()
            list($attribut,$id)=explode('_',$key);
            // Si l'utilisateur n'a rien tapé
            if (empty($value)){
                continue;
            }
            // Si la case 'isConsumable' est cochée et qu'une quantité à été associée
            if ($attribut==="isConsumable" && $value==="1" && $id===$lastId){
                end($usedPieces)->setIsConsumable(True);
            }
            // Si une quantité a été rentrée
            elseif ($attribut==="quantity"){
                // On crée un nouveau UsedPieces
                $usedPiece = new UsedPieces();
                $usedPiece->setQuantity(floatval($value));
                $usedPiece->setFkIntervention($intervention);
                $usedPiece->setFkPiece($piecesRepository->findOneBy(['id' => $id]));
                $usedPiece->setIsConsumable(False);

                $usedPieces[]=$usedPiece;
                $lastId=$id;    //Pour pouvoir associer le 'isConsumable' qui suivra s'il a été coché
            }
        }

        //Ajout dans la table UsedPieces de chaque pièce utilisée
        // !! à faire : si une pièce a déjà été utilisée, potentiellement venir mettre à jour la ligne plutôt que de rajouter une ligne
        foreach($usedPieces as $usedPiece){
            $entityManager->persist($usedPiece);
            //vérification du stock suffisant
            $piece=$usedPiece->getFkPiece();
            $new_quantity=$piece->getQuantity()-$usedPiece->getQuantity();
            if ($new_quantity<0){
                $this->addFlash('danger',"Stock insuffisant !");
                return $this->redirectToRoute('app_used_pieces_use_piece',['id'=>$intervention->getId()]);
            }

            // Décrément du stock si isConsumable
            if ($usedPiece->isConsumable()){
                $piece->setQuantity($new_quantity);
                $entityManager->persist($piece);
            }
        }

        // Mise à jour de la BDD
        $entityManager->flush();

        $this->addFlash('success',"Prise en compte de l'utilisation du matériel");
        return $this->redirectToRoute('app_used_pieces_use_piece',['id'=>$intervention->getId()]);
    }

}
