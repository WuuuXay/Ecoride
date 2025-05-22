<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Form\VoitureType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VoitureController extends AbstractController
{
    #[Route('/voiture/ajouter', name: 'ajouter_voiture')]
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $voiture = new Voiture();
        $form = $this->createForm(VoitureType::class, $voiture);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $voiture->setProprietaire($this->getUser());
            $em->persist($voiture);
            $em->flush();

            $this->addFlash('success', 'Votre véhicule a été ajouté avec succès!');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('voiture/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/voiture/modifier/{id}', name: 'modifier_voiture')]
    public function modifier(Request $request, EntityManagerInterface $em, Voiture $voiture): Response
    {
        // Vérification que l'utilisateur est bien propriétaire
        if ($voiture->getProprietaire() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(VoitureType::class, $voiture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Véhicule modifié avec succès');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('voiture/modifier.html.twig', [
            'form' => $form->createView(),
            'voiture' => $voiture
        ]);
    }

    #[Route('/voiture/supprimer/{id}', name: 'supprimer_voiture')]
    public function supprimer(EntityManagerInterface $em, Voiture $voiture): Response
    {
        // Vérification que l'utilisateur est bien propriétaire
        if ($voiture->getProprietaire() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $em->remove($voiture);
        $em->flush();
        $this->addFlash('success', 'Véhicule supprimé avec succès');

        return $this->redirectToRoute('dashboard');
    }
}