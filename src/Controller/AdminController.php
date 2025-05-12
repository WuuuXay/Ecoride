<?php

namespace App\Controller;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(UtilisateurRepository $utilisateurRepo): Response
    {
        $utilisateurs = $utilisateurRepo->findAll();

        return $this->render('admin/dashboard.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
}
