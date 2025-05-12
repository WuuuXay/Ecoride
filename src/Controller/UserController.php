<?php

namespace App\Controller;

use App\Entity\Participation;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'user_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $mesCovoiturages = $em->getRepository(Covoiturage::class)->findBy(['chauffeur' => $user]);
        $mesParticipations = $em->getRepository(Participation::class)->findBy(['passager' => $user]);

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'mesCovoiturages' => $mesCovoiturages,
            'mesParticipations' => $mesParticipations,
        ]);
    }
}

