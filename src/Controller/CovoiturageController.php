<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Participation;
use App\Form\RechercheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CovoiturageRepository;

class CovoiturageController extends AbstractController
{
    #[Route('/covoiturages', name: 'covoiturages')]
    public function index(Request $request, CovoiturageRepository $repository): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        $covoiturages = $repository->findAll();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $covoiturages = $repository->findByDepartArriveeDate(
                $data['depart'] ?? null,
                $data['arrivee'] ?? null,
                $data['date'] ?? null
            );
        }

        return $this->render('covoiturage/index.html.twig', [
            'form' => $form->createView(),
            'covoiturages' => $covoiturages,
        ]);
    }

    #[Route('/covoiturages/carte', name: 'covoiturage_map')]
    public function carte(EntityManagerInterface $em): Response
    {
        $trajets = $em->getRepository(Covoiturage::class)->findAll();

        $trajetData = [];
        foreach ($trajets as $trajet) {
            $trajetData[] = [
                'id' => $trajet->getId(),
                'villeDepart' => $trajet->getDepart(),
                'villeArrivee' => $trajet->getArrivee(),
                'conducteur' => $trajet->getChauffeur()->getPseudo(),
                'prix' => $trajet->getPrix(),
                'date' => $trajet->getDateDepart()->format('d/m/Y H:i'),
                'coordDepart' => ['lat' => 48.8566, 'lon' => 2.3522],
                'coordArrivee' => ['lat' => 45.7640, 'lon' => 4.8357],
            ];
        }

        return $this->render('covoiturage/carte.html.twig', [
            'trajets' => $trajetData,
        ]);
    }

    #[Route('/covoiturages/detail/{id}', name: 'covoiturage_detail')]
    public function detail(Covoiturage $covoiturage): Response
    {
        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage' => $covoiturage,
        ]);
    }

    #[Route('/covoiturages/participer/{id}', name: 'covoiturage_participer', methods: ['POST'])]
    public function participer(
        Covoiturage $covoiturage, 
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // Vérifications
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour participer');
            return $this->redirectToRoute('login');
        }

        if ($user === $covoiturage->getChauffeur()) {
            $this->addFlash('error', 'Vous ne pouvez pas participer à votre propre trajet');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        if ($covoiturage->getPlacesDisponibles() <= 0) {
            $this->addFlash('error', 'Plus de places disponibles');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        if ($user->getCredits() < $covoiturage->getPrix()) {
            $this->addFlash('error', 'Crédits insuffisants');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        // Transaction
        $em->beginTransaction();
        try {
            // Déduire les crédits
            $user->setCredits($user->getCredits() - $covoiturage->getPrix());

            // Réduire les places
            $covoiturage->setPlacesDisponibles($covoiturage->getPlacesDisponibles() - 1);

            // Enregistrer la participation
            $participation = new Participation();
            $participation->setPassager($user)
                ->setCovoiturage($covoiturage)
                ->setConfirme(true);

            $em->persist($participation);
            $em->flush();
            $em->commit();

            $this->addFlash('success', 'Participation enregistrée !');
        } catch (\Exception $e) {
            $em->rollback();
            $this->addFlash('error', 'Une erreur est survenue: '.$e->getMessage());
        }

        return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
    }
}