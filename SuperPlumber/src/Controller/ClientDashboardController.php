<?php

namespace App\Controller;

use App\Entity\Interventions;
use App\Enum\Status;
use App\Enum\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class ClientDashboardController extends AbstractController
{
    #[Route('/client/dashboard', name: 'app_client_dashboard', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $titre = $request->request->get('titre_intervention');
            $description = $request->request->get('description');

            if (!empty($titre)) {
                // Mapping des valeurs du formulaire aux valeurs de l'enum Type
                $typeMapping = [
                    'fuite' => Type::FUITE,
                    'debouchage' => Type::DEBOUCHAGE,
                    'chauffe_eau' => Type::REPARATION,
                    'installation' => Type::INSTALLATION,
                ];

                $intervention = new Interventions();
                $intervention->setType($typeMapping[$titre]);
                $intervention->setDescription($description);

                $intervention->setStatus(Status::TO_PLAN);
                $intervention->setfkClient($this->getUser());
                $intervention->setDate(new \DateTime());

                // sauvegarde doctrine
                $em->persist($intervention);
                $em->flush();

                $this->addFlash('success', 'Votre demande d\'intervention est en attente de validation.');

                return $this->redirectToRoute('app_client_dashboard');
            }
        }

        $mesInterventions = $em->getRepository(Interventions::class)->findBy(
            ['fkClient' => $this->getUser()],
            ['date' => 'DESC']
        );

        return $this->render('clients/dashboard.html.twig', [
            'interventions' => $mesInterventions
        ]);
    }
}
