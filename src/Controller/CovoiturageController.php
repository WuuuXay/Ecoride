<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Participation;
use App\Form\RechercheType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

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
                $data['date'] ?? null,
                $data['prixMax'] ?? null,
                $data['ecologique'] ?? null
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

        $trajetData = array_map(function ($trajet) {
            return [
                'id' => $trajet->getId(),
                'villeDepart' => $trajet->getDepart(),
                'villeArrivee' => $trajet->getArrivee(),
                'conducteur' => $trajet->getChauffeur()->getPseudo(),
                'prix' => $trajet->getPrix(),
                'date' => $trajet->getDateDepart()->format('d/m/Y H:i'),
                'coordDepart' => ['lat' => 48.8566, 'lon' => 2.3522], // TODO: remplacer par géoloc réelle
                'coordArrivee' => ['lat' => 45.7640, 'lon' => 4.8357],
            ];
        }, $trajets);

        return $this->render('covoiturage/carte.html.twig', [
            'trajets' => $trajetData,
        ]);
    }

    #[Route('/covoiturages/detail/{id}', name: 'covoiturage_detail')]
    public function detail(Covoiturage $covoiturage): Response
    {
        $user = $this->getUser();
        $estParticipant = false;

        if ($user) {
            $estParticipant = $covoiturage->isUserParticipant($user);
        }

        return $this->render('covoiturage/detail.html.twig', [
            'covoiturage' => $covoiturage,
            'chauffeur' => $covoiturage->getChauffeur(),
            'estParticipant' => $estParticipant,
        ]);
    }

    #[Route('/covoiturages/participer/{id}', name: 'covoiturage_participer', methods: ['POST'])]
    public function participer(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

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

        try {
            $user->setCredits($user->getCredits() - $covoiturage->getPrix());
            $covoiturage->setPlacesDisponibles($covoiturage->getPlacesDisponibles() - 1);

            $participation = new Participation();
            $participation->setPassager($user)
                          ->setCovoiturage($covoiturage)
                          ->setConfirme(true);

            $em->persist($participation);
            $em->persist($user);
            $em->persist($covoiturage);
            $em->flush();

            $this->addFlash('success', 'Participation enregistrée !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }

        return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
    }

    #[Route('/covoiturages/reserver/{id}', name: 'covoiturage_reserver')]
    public function reserver(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté');
            return $this->redirectToRoute('login');
        }

        if ($user === $covoiturage->getChauffeur()) {
            $this->addFlash('error', 'Vous ne pouvez pas réserver votre propre trajet');
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

        $participation = new Participation();
        $participation->setPassager($user)
                      ->setCovoiturage($covoiturage)
                      ->setConfirme(false);

        $em->persist($participation);
        $em->flush();

        return $this->render('covoiturage/confirmation.html.twig', [
            'covoiturage' => $covoiturage,
            'participation' => $participation,
        ]);
    }

    #[Route('/covoiturages/confirmer/{id}', name: 'covoiturage_confirmer', methods: ['POST'])]
    public function confirmer(Participation $participation, EntityManagerInterface $em): Response
    {
        if ($participation->getPassager() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($participation->isConfirme()) {
            $this->addFlash('warning', 'Réservation déjà confirmée');
            return $this->redirectToRoute('dashboard');
        }

        try {
            $covoiturage = $participation->getCovoiturage();
            $user = $participation->getPassager();

            $user->setCredits($user->getCredits() - $covoiturage->getPrix());
            $covoiturage->setPlacesDisponibles($covoiturage->getPlacesDisponibles() - 1);
            $participation->setConfirme(true);

            $em->persist($user);
            $em->persist($covoiturage);
            $em->persist($participation);
            $em->flush();

            $this->addFlash('success', 'Réservation confirmée !');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la confirmation: ' . $e->getMessage());
        }

        return $this->redirectToRoute('dashboard');
    }

    #[Route('/covoiturages/historique', name: 'covoiturage_historique')]
    public function historique(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $covoituragesOrganises = $em->getRepository(Covoiturage::class)
            ->createQueryBuilder('c')
            ->where('c.chauffeur = :user')
            ->andWhere('c.dateDepart < :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.dateDepart', 'DESC')
            ->getQuery()
            ->getResult();

        $participationsPassees = $em->getRepository(Participation::class)
            ->createQueryBuilder('p')
            ->join('p.covoiturage', 'c')
            ->where('p.passager = :user')
            ->andWhere('c.dateDepart < :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->orderBy('c.dateDepart', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('covoiturage/historique.html.twig', [
            'covoituragesOrganises' => $covoituragesOrganises,
            'participationsPassees' => $participationsPassees,
        ]);
    }

    #[Route('/covoiturages/annuler/{id}', name: 'covoiturage_annuler', methods: ['POST'])]
    public function annuler(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user !== $covoiturage->getChauffeur()) {
            $this->addFlash('error', 'Vous n\'avez pas le droit d\'annuler ce trajet');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        try {
            $em->remove($covoiturage);
            $em->flush();

            $this->addFlash('success', 'Trajet annulé');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }

        return $this->redirectToRoute('covoiturages');
    }

    #[Route('/covoiturages/debuter/{id}', name: 'covoiturage_debuter', methods: ['POST'])]
    public function debuter(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user !== $covoiturage->getChauffeur()) {
            $this->addFlash('error', 'Vous ne pouvez pas démarrer ce trajet');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        $covoiturage->setStatut('en_cours');
        $em->persist($covoiturage);
        $em->flush();

        $this->addFlash('success', 'Trajet démarré');
        return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
    }

    #[Route('/covoiturages/terminer/{id}', name: 'covoiturage_terminer', methods: ['POST'])]
    public function terminer(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user !== $covoiturage->getChauffeur()) {
            $this->addFlash('error', 'Vous ne pouvez pas terminer ce trajet');
            return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
        }

        $covoiturage->setStatut('termine');
        $em->persist($covoiturage);
        $em->flush();

        $this->addFlash('success', 'Trajet terminé');
        return $this->redirectToRoute('covoiturage_detail', ['id' => $covoiturage->getId()]);
    }
}
