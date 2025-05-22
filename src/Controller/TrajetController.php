<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Entity\Voiture; 
use App\Form\CovoiturageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrajetController extends AbstractController
{
    #[Route('/trajet/new', name: 'trajet')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($user->getVoitures()->isEmpty()) {
            $this->addFlash('warning', 'Vous devez ajouter un véhicule avant de créer un trajet.');
            return $this->redirectToRoute('ajouter_voiture'); 
        }

        $trajet = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $trajet, [
            'user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setChauffeur($user); 
            $em->persist($trajet);
            $em->flush();

            return $this->redirectToRoute('dashboard');
        }

        return $this->render('trajet/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}