<?php

namespace App\Controller;

use App\Form\RechercheType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(RechercheType::class);
        $form->handleRequest($request);

        // Ajout de la vérification de connexion et des rôles
        $user = $this->getUser();
        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $isEmploye = $this->isGranted('ROLE_EMPLOYE');

        return $this->render('home/index.html.twig', [
            'search_form' => $form->createView(),
            'user' => $user,
            'is_admin' => $isAdmin,
            'is_employe' => $isEmploye
        ]);
    }
}