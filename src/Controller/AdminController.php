<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\EmployeRegistrationType;
use App\Repository\CovoiturageRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(
        UtilisateurRepository $utilisateurRepo,
        CovoiturageRepository $covoiturageRepo
    ): Response {
        $stats = [
            'totalUsers' => $utilisateurRepo->count([]),
            'totalEmployes' => $utilisateurRepo->countByRole('ROLE_EMPLOYE'),
            'totalAdmins' => $utilisateurRepo->countByRole('ROLE_ADMIN'),
            'totalCredits' => array_sum(array_map(fn($u) => $u->getCredits(), $utilisateurRepo->findAll())),
            'newUsers' => $utilisateurRepo->findLastRegistered(5)
        ];

        return $this->render('admin/dashboard.html.twig', [
            'utilisateurs' => $utilisateurRepo->findAll(),
            'stats' => $stats,
            'covoiturageStats' => $this->getCovoiturageStats($covoiturageRepo)
        ]);
    }

    #[Route('/admin/employe/new', name: 'admin_create_employe')]
    public function createEmploye(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $employe = new Utilisateur();
        $form = $this->createForm(EmployeRegistrationType::class, $employe);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Hash du mot de passe
            $hashedPassword = $passwordHasher->hashPassword(
                $employe,
                $form->get('password')->getData()
            );
            
            $employe->setPassword($hashedPassword)
                    ->setRoles(['ROLE_EMPLOYE'])
                    ->setIsActive(true);

            $em->persist($employe);
            $em->flush();

            $this->addFlash('success', 'Employé créé avec succès');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/create_employe.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/user/toggle/{id}', name: 'admin_toggle_user')]
    public function toggleUser(Utilisateur $user, EntityManagerInterface $em): Response
    {
        if ($this->getUser()->getId() === $user->getId()) {
            $this->addFlash('danger', 'Vous ne pouvez pas désactiver votre propre compte');
            return $this->redirectToRoute('admin_dashboard');
        }

        $user->setIsActive(!$user->isActive());
        $em->flush();

        $this->addFlash('success', sprintf(
            'Compte %s %s', 
            $user->getPseudo(), 
            $user->isActive() ? 'activé' : 'désactivé'
        ));
        return $this->redirectToRoute('admin_dashboard');
    }

#[Route('/admin/user/promote/{id}', name: 'admin_promote_user')]
public function promoteToEmploye(Utilisateur $user, EntityManagerInterface $em): Response
{
    $roles = $user->getRoles();
    if (!in_array('ROLE_EMPLOYE', $roles)) {
        $roles[] = 'ROLE_EMPLOYE';
        $user->setRoles($roles);
        $em->flush();
        $this->addFlash('success', 'Utilisateur promu employé');
    }

    return $this->redirectToRoute('admin_dashboard');
}

    private function getCovoiturageStats(CovoiturageRepository $repo): array
    {
        return [
            'dates' => $repo->findLastWeekDates(),
            'counts' => $repo->findLastWeekCounts(),
            'credits' => $repo->findLastWeekCredits()
        ];
    }
}