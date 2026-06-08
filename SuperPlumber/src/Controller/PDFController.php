<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Repository\InterventionsRepository;
use App\Service\PdfGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/public')]
final class PDFController extends AbstractController
{
    #[Route('/pdf/{id}', name: 'app_public_generatePDF', methods: ['GET'])]
    public function generatePDF(Interventions $intervention, InterventionsRepository $interventionsRepository, PdfGeneratorService $pdf): Response
    {
        $userID = $this->getUser()->getId();
        // Vérifier que l'user peut accéder au détail de l'intervention
        if (($intervention->getFkClient()?->getId() === $userID) or ($intervention->getFkEmployee()?->getId() === $userID) or ($this->isGranted('ROLE_ADMIN'))) {
            // Création du template html pour le pdf
            $html = $this->renderView('interventions/pdf.html.twig', [
                'intervention' => $intervention,
                'plumber_name' => $intervention->getFkEmployee()?->getFullName(),
                'client' => $intervention->getFkClient(),
            ]);
            // Création du pdf à partir du pdf
            $pdfContent = $pdf->generate($html);
            // Retourner le pdf en téléchargement
            return new Response(
                $pdfContent,
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="intervention-'
                        . $intervention->getId()
                        . '-' . $intervention->getFkClient()->getFullName() . '.pdf"',
                ]
            );
        } else {
            throw $this->createAccessDeniedException();
        }

        return $this->render('/.html.twig', [
            'interventions' => $interventionsRepository->findAll(),
        ]);
    }
}
