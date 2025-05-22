<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Participation;
use App\Entity\Covoiturage;
use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Form\VoitureType;
use App\Form\AvisType;
use App\Form\UserType;
use App\Form\UserRoleType;
use App\Form\ChauffeurPreferencesType;
use App\Repository\AvisRepository;
use App\Repository\ParticipationRepository;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $mesCovoiturages = $em->getRepository(Covoiturage::class)->findBy(['chauffeur' => $user]);
        $mesParticipations = $em->getRepository(Participation::class)->findBy(['passager' => $user]);

        return $this->render('user/dashboard.html.twig', [
            'user' => $user,
            'mesCovoiturages' => $mesCovoiturages,
            'mesParticipations' => $mesParticipations,
            'mesVehicules' => $user->getVoitures()
        ]);
    }

    #[Route('/mon-profil', name: 'mon_profil')]
    public function profil(
        EntityManagerInterface $em,
        AvisRepository $avisRepo,
        ParticipationRepository $participationRepo,
        Request $request
    ): Response {
        $utilisateur = $this->getUser();
        
        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $avisForm = null;
        $canLeaveReview = false;
        $user = $this->getUser();


    if ($user && $user !== $utilisateur) {
        $hasSharedRide = $participationRepo->hasSharedCovoiturage($user, $utilisateur);
        if ($hasSharedRide) {
            $avis = new Avis();
            $avis->setAuteur($user);
            $avis->setCible($utilisateur);
            $form = $this->createForm(AvisType::class, $avis);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($avis);
                $em->flush();
                $this->addFlash('success', 'Avis publié.');
                return $this->redirectToRoute('utilisateur_profil', ['id' => $utilisateur->getId()]);
            }

            $avisForm = $form->createView();
            $canLeaveReview = true;
        }
    }

return $this->render('user/profil.html.twig', [
            'utilisateur' => $utilisateur,
            'avisList' => $avisRepo->findBy(['cible' => $utilisateur]),
            'avisForm' => $avisForm,
            'canLeaveReview' => $canLeaveReview
        ]);
    }

    #[Route('/profil/edit', name: 'edit_profile')]
    public function editProfile(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoProfil')->getData();
            if ($photoFile) {
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $user->setPhotoProfil($newFilename);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Profil mis à jour');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('user/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/covoiturage/annuler/{id}', name: 'annuler_covoiturage')]
    public function annulerCovoiturage(
        Covoiturage $covoiturage,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = $this->getUser();

        if ($covoiturage->getChauffeur() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le chauffeur de ce covoiturage.');
        }

        $covoiturage->setAnnule(true);

        foreach ($covoiturage->getParticipations() as $participation) {
            if ($participation->isConfirme()) {
                $passager = $participation->getPassager();
                $passager->addCredits($covoiturage->getPrix());
                $participation->setAnnule(true);

                if ($passager->getEmail()) {
                    $email = (new Email())
                        ->from('no-reply@ecoride.fr')
                        ->to($passager->getEmail())
                        ->subject('Annulation du covoiturage')
                        ->html("
                            Bonjour {$passager->getPseudo()},<br><br>
                            Le covoiturage prévu de <strong>{$covoiturage->getDepart()}</strong> à <strong>{$covoiturage->getArrivee()}</strong>
                            le <strong>{$covoiturage->getDateDepart()->format('d/m/Y H:i')}</strong> a été annulé par le conducteur.<br><br>
                            Vos crédits ont été automatiquement recrédités.
                        ");
                    $mailer->send($email);
                }
            }
        }

        $em->flush();

        $this->addFlash('success', 'Le covoiturage a bien été annulé et les passagers informés.');
        return $this->redirectToRoute('dashboard');
    }

    #[Route('/participation/annuler/{id}', name: 'annuler_participation')]
    public function annulerParticipation(
        Participation $participation,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if ($participation->getPassager() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas le passager de ce trajet.');
        }

        if ($participation->isConfirme()) {
            $user->addCredits($participation->getCovoiturage()->getPrix());
            $participation->getCovoiturage()->setPlacesDisponibles(
                $participation->getCovoiturage()->getPlacesDisponibles() + 1
            );
        }

        $participation->setAnnule(true);

        $em->flush();

        $this->addFlash('success', 'Votre participation a été annulée.');
        return $this->redirectToRoute('dashboard');
    }

#[Route('/profil/roles', name: 'profil_roles')]
public function editRoles(Request $request, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $form = $this->createForm(UserRoleType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Mise à jour des rôles en fonction des cases cochées
        $roles = $user->getRoles();
        if ($user->isChauffeur() && !in_array('ROLE_CHAUFFEUR', $roles)) {
            $roles[] = 'ROLE_CHAUFFEUR';
        } elseif (!$user->isChauffeur() && in_array('ROLE_CHAUFFEUR', $roles)) {
            $roles = array_diff($roles, ['ROLE_CHAUFFEUR']);
        }
        $user->setRoles($roles);

        $em->flush();

        if ($user->isChauffeur()) {
            return $this->redirectToRoute('profil_chauffeur_preferences');
        }

        $this->addFlash('success', 'Vos préférences de rôle ont été mises à jour');
        return $this->redirectToRoute('dashboard');
    }

    return $this->render('user/edit_roles.html.twig', [
        'form' => $form->createView(),
    ]);
}

    #[Route('/profil/chauffeur-preferences', name: 'profil_chauffeur_preferences')]
    public function chauffeurPreferences(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        
        if (!$user->isChauffeur()) {
            return $this->redirectToRoute('profil_roles');
        }

        $form = $this->createForm(ChauffeurPreferencesType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Vos préférences de chauffeur ont été enregistrées');
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('user/chauffeur_preferences.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
