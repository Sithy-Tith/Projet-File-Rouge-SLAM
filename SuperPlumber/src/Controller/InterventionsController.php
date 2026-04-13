<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Entity\UsedPieces;
use App\Form\InterventionsType;
use App\Repository\InterventionsRepository;
use App\Repository\PiecesRepository;
use App\Repository\UsedPiecesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/interventions')]
final class InterventionsController extends AbstractController
{
    #[Route(name: 'app_interventions_index', methods: ['GET'])]
    public function index(InterventionsRepository $interventionsRepository): Response
    {
        return $this->render('interventions/index.html.twig', [
            'interventions' => $interventionsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_interventions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $intervention = new Interventions();
        $form = $this->createForm(InterventionsType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($intervention);
            $entityManager->flush();

            return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('interventions/new.html.twig', [
            'intervention' => $intervention,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_interventions_show', methods: ['GET'])]
    public function show(Interventions $intervention): Response
    {
        return $this->render('interventions/show.html.twig', [
            'intervention' => $intervention,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_interventions_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Interventions $intervention, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InterventionsType::class, $intervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('interventions/edit.html.twig', [
            'intervention' => $intervention,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_interventions_delete', methods: ['POST'])]
    public function delete(Request $request, Interventions $intervention, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $intervention->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($intervention);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_interventions_index', [], Response::HTTP_SEE_OTHER);
    }



// Après soumission du formulaire d'utilisation des pièces
// Update de la table UsedPieces et mise à jour du stock de la pièce si consommable
    #[Route('/{id}/use_piece_form_submitted', name: 'app_interventions_use_piece_submitted', methods: ['POST'])]
    public function use_piece_submitted(
        Interventions $intervention,    //Intervention actuelle, mapped by {id} dans la route
        PiecesRepository $piecesRepository, //Le repo des pieces pour pouvoir récupérer les infos d'une pièce
        EntityManagerInterface $entityManager)  //Manager pour pouvoir mettre à jour la BDD
    {
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
            // Décrément du stock si isConsumable
            if ($usedPiece->isConsumable()){
                $piece=$usedPiece->getFkPiece();

                $new_quantity=$piece->getQuantity()-$usedPiece->getQuantity();
                if ($new_quantity<0){
                    $this->addFlash('danger',"Stock insuffisant !");
                    return $this->redirectToRoute('app_interventions_use_piece',['id'=>$intervention->getId()]);
                }

                $piece->setQuantity($new_quantity);
                $entityManager->persist($piece);
            }
        }

        // Mise à jour de la BDD
        $entityManager->flush();

        $this->addFlash('success',"Prise en compte de l'utilisation du matériel");
        return $this->redirectToRoute('app_interventions_use_piece',['id'=>$intervention->getId()]);
    }
}
