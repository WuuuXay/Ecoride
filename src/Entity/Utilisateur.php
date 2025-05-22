<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column]
    private $password;

    #[ORM\Column(length: 50)]
    private $pseudo;

    #[ORM\Column(type: 'integer')]
    private $credits = 20;

    #[ORM\Column(nullable: true)]
    private ?string $telephone = null;

    #[ORM\Column(nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isChauffeur = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isPassager = true;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $accepteFumeurs = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $accepteAnimaux = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $preferencesSupplementaires = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'proprietaire', targetEntity: Voiture::class, orphanRemoval: true)]
    private Collection $voitures;

    #[ORM\OneToMany(mappedBy: 'auteur', targetEntity: Avis::class, orphanRemoval: true)]
    private Collection $avisDonnes;

    #[ORM\OneToMany(mappedBy: 'cible', targetEntity: Avis::class, orphanRemoval: true)]
    private Collection $avisRecus;

    #[ORM\OneToMany(mappedBy: 'chauffeur', targetEntity: Covoiturage::class, orphanRemoval: true)]
    private Collection $covoituragesOrganises;

    #[ORM\OneToMany(mappedBy: 'passager', targetEntity: Participation::class, orphanRemoval: true)]
    private Collection $participations;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photoProfil;

    #[ORM\Column(type: 'float', nullable: true)]
    private $noteMoyenne = 0;

      public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }

    public function setPhotoProfil(?string $photoProfil): self
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }

    public function getNoteMoyenne(): ?float
    {
        return $this->noteMoyenne;
    }

    public function setNoteMoyenne(?float $noteMoyenne): self
    {
        $this->noteMoyenne = $noteMoyenne;
        return $this;
    }

    public function updateNoteMoyenne(): void
    {
        $avis = $this->getAvisRecus();
        if ($avis->isEmpty()) {
            $this->noteMoyenne = 0;
            return;
        }

        $total = 0;
        foreach ($avis as $avi) {
            $total += $avi->getNote();
        }
        $this->noteMoyenne = $total / $avis->count();
    }

    public function __construct()
    {
        $this->voitures = new ArrayCollection();
        $this->avisDonnes = new ArrayCollection();
        $this->avisRecus = new ArrayCollection();
        $this->covoituragesOrganises = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function hasRole(string $role): bool
{
    return in_array($role, $this->getRoles(), true);
}

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;
        return $this;
    }

    public function getCredits(): int
    {
        return $this->credits;
    }

    public function setCredits(int $credits): self
    {
        $this->credits = $credits;
        return $this;
    }

    public function deductCredits(float $amount): bool
    {
        if ($this->credits >= $amount) {
            $this->credits -= $amount;
            return true;
        }
        return false;
    }

    public function addCredits(float $amount): self
    {
        $this->credits += $amount;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Efface les données temporaires sensibles
    }

    public function getVoitures(): Collection
    {
        return $this->voitures;
    }

    public function addVoiture(Voiture $voiture): self
    {
        if (!$this->voitures->contains($voiture)) {
            $this->voitures[] = $voiture;
            $voiture->setProprietaire($this);
        }
        return $this;
    }

    public function removeVoiture(Voiture $voiture): self
    {
        if ($this->voitures->removeElement($voiture)) {
            if ($voiture->getProprietaire() === $this) {
                $voiture->setProprietaire(null);
            }
        }
        return $this;
    }

    public function getAvisDonnes(): Collection
    {
        return $this->avisDonnes;
    }

    public function getAvisRecus(): Collection
    {
        return $this->avisRecus;
    }

    public function getCovoituragesOrganises(): Collection
    {
        return $this->covoituragesOrganises;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }
public function isChauffeur(): bool
{
    return in_array('ROLE_CHAUFFEUR', $this->getRoles()) || $this->isChauffeur;
}

    public function setIsChauffeur(bool $isChauffeur): self
    {
        $this->isChauffeur = $isChauffeur;
        return $this;
    }

    public function isPassager(): bool
    {
        return $this->isPassager;
    }

    public function setIsPassager(bool $isPassager): self
    {
        $this->isPassager = $isPassager;
        return $this;
    }

    public function getAccepteFumeurs(): ?bool
    {
        return $this->accepteFumeurs;
    }

    public function setAccepteFumeurs(?bool $accepteFumeurs): self
    {
        $this->accepteFumeurs = $accepteFumeurs;
        return $this;
    }

    public function getAccepteAnimaux(): ?bool
    {
        return $this->accepteAnimaux;
    }

    public function setAccepteAnimaux(?bool $accepteAnimaux): self
    {
        $this->accepteAnimaux = $accepteAnimaux;
        return $this;
    }

    public function getPreferencesSupplementaires(): ?string
    {
        return $this->preferencesSupplementaires;
    }

    public function setPreferencesSupplementaires(?string $preferencesSupplementaires): self
    {
        $this->preferencesSupplementaires = $preferencesSupplementaires;
        return $this;
    }

    public function getRoleDisplay(): string
    {
        if ($this->isChauffeur && $this->isPassager) {
            return 'Chauffeur & Passager';
        }
        return $this->isChauffeur ? 'Chauffeur' : 'Passager';
    }
}




