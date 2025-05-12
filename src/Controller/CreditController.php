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


class CreditController extends AbstractController
{

    #[Route('/credits/ajouter', name: 'ajouter_credits')]
    public function ajouterCredits(Request $request, EntityManagerInterface $em): Response
    {
     $form = $this->createFormBuilder()
        ->add('montant', NumberType::class, [
            'label' => 'Montant à ajouter',
            'html5' => true,
            'attr' => ['min' => 10, 'step' => 5]
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $this->getUser();
        $user->setCredits($user->getCredits() + $form->getData()['montant']);
        $em->flush();

        $this->addFlash('success', 'Crédits ajoutés avec succès !');
        return $this->redirectToRoute('dashboard');
    }

    return $this->render('user/ajouter_credits.html.twig', [
        'form' => $form->createView()
    ]);
}
}