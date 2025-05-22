<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Entity\Voiture;
use App\Entity\Covoiturage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void // <- ✅ CORRECTION ICI
    {
        // Utilisateur
        $user = new Utilisateur();
        $user->setEmail('user@ecoride.fr');
        $user->setPseudo('JohnDoe');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        // Admin
        $admin = new Utilisateur();
        $admin->setEmail('admin@ecoride.fr');
        $admin->setPseudo('AdminBoss');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Employé
        $employe = new Utilisateur();
        $employe->setEmail('employe@ecoride.fr');
        $employe->setPseudo('EmployeeOne');
        $employe->setPassword($this->passwordHasher->hashPassword($employe, 'employepass'));
        $employe->setRoles(['ROLE_EMPLOYE']);
        $manager->persist($employe);

        // Voiture
        $voiture = new Voiture();
        $voiture->setMarque('Tesla')
                ->setModele('Model 3')
                ->setCouleur('Blanc')
                ->setEnergie('Électrique')
                ->setPlaqueImmatriculation('AA-123-AA')
                ->setNbPlaces(4)
                ->setProprietaire($user);
        $manager->persist($voiture);

        // Covoiturage
        $covoiturage = new Covoiturage();
        $covoiturage->setDepart('Paris')
                    ->setArrivee('Lyon')
                    ->setDateDepart(new \DateTime('+1 day'))
                    ->setDateArrivee(new \DateTime('+1 day +5 hours'))
                    ->setPrix(25)
                    ->setPlacesDisponibles(3)
                    ->setChauffeur($user)
                    ->setVoiture($voiture)
                    ->setEcologique(true);
        $manager->persist($covoiturage);

        $manager->flush();
    }
}
