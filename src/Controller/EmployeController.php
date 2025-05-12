<?php

namespace App\Controller;

use App\Repository\AvisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'employe_dashboard')]
    public function dashboard(AvisRepository $avisRepo): Response
    {
        $avisAValider = $avisRepo->findBy(['valide' => false]);

        return $this->render('employe/dashboard.html.twig', [
            'avisAValider' => $avisAValider,
        ]);
    }
}
