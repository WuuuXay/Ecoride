<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Covoiturage;
use App\Repository\AvisRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYE')]
class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'employe_dashboard')]
    public function dashboard(AvisRepository $avisRepo, CovoiturageRepository $covoiturageRepo): Response
    {
        return $this->render('employe/dashboard.html.twig', [
            'avisAValider' => $avisRepo->findBy(['valide' => false]),
            'incidents' => $covoiturageRepo->findBy(['incident' => true], ['dateDepart' => 'DESC'], 5)
        ]);
    }

    #[Route('/employe/avis', name: 'employe_avis')]
    public function gestionAvis(AvisRepository $avisRepo): Response
    {
        return $this->render('employe/avis.html.twig', [
            'avisAValider' => $avisRepo->findBy(['valide' => false], ['dateCreation' => 'ASC']),
            'avisValides' => $avisRepo->findBy(['valide' => true], ['dateCreation' => 'DESC'])
        ]);
    }

    #[Route('/employe/avis/validate/{id}', name: 'employe_validate_avis', methods: ['POST'])]
    public function validateAvis(Avis $avis, EntityManagerInterface $em): Response
    {
        $avis->setValide(true);
        $em->flush();

        $this->addFlash('success', 'Avis validé et publié');
        return $this->redirectToRoute('employe_avis');
    }

    #[Route('/employe/avis/reject/{id}', name: 'employe_reject_avis', methods: ['POST'])]
    public function rejectAvis(Avis $avis, EntityManagerInterface $em, Request $request): Response
    {
        $reason = $request->request->get('rejection_reason', 'Non conforme');
        
        $avis->setCommentaire("[REJETÉ - $reason] " . $avis->getCommentaire());
        $avis->setValide(false);
        $em->flush();

        $this->addFlash('warning', 'Avis rejeté');
        return $this->redirectToRoute('employe_avis');
    }

    #[Route('/employe/incidents', name: 'employe_incidents')]
    public function incidents(CovoiturageRepository $covoiturageRepo): Response
    {
        return $this->render('employe/incidents.html.twig', [
            'incidents' => $covoiturageRepo->findBy(['incident' => true], ['dateDepart' => 'DESC'])
        ]);
    }

    #[Route('/employe/incident/{id}', name: 'employe_incident_detail')]
    public function incidentDetail(Covoiturage $covoiturage): Response
    {
        return $this->render('employe/incident_detail.html.twig', [
            'covoiturage' => $covoiturage
        ]);
    }
}