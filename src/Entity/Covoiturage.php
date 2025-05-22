<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\CovoiturageRepository")]
class Covoiturage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(length: 255)]
    private $depart;

    #[ORM\Column(length: 255)]
    private $arrivee;

    #[ORM\Column(type: 'datetime')]
    private $dateDepart;

    #[ORM\Column(type: 'datetime')]
    private $dateArrivee;

    #[ORM\Column(type: 'float')]
    private $prix;

    #[ORM\Column(type: 'integer')]
    private $placesDisponibles;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'covoituragesOrganises')]
    private $chauffeur;

    #[ORM\ManyToOne(targetEntity: Voiture::class, inversedBy: 'covoiturages')]
    private $voiture;

    #[ORM\Column(type: 'string', length: 20)]
    private $statut = 'a_venir';

    #[ORM\Column(type: 'boolean')]
    private $ecologique = false;

    #[ORM\Column(type: 'boolean')]
    private $incident = false;

    #[ORM\Column(type: 'boolean')]
    private $annule = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private $descriptionIncident;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'covoiturage', targetEntity: Participation::class)]
    private $participations;

    #[ORM\OneToMany(mappedBy: 'covoiturage', targetEntity: Avis::class)]
    private $avis;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getDepart(): ?string { return $this->depart; }
    public function setDepart(string $depart): self { $this->depart = $depart; return $this; }
    public function getArrivee(): ?string { return $this->arrivee; }
    public function setArrivee(string $arrivee): self { $this->arrivee = $arrivee; return $this; }
    public function getDateDepart(): ?\DateTimeInterface { return $this->dateDepart; }
    public function setDateDepart(\DateTimeInterface $dateDepart): self { $this->dateDepart = $dateDepart; return $this; }
    public function getDateArrivee(): ?\DateTimeInterface { return $this->dateArrivee; }
    public function setDateArrivee(\DateTimeInterface $dateArrivee): self { $this->dateArrivee = $dateArrivee; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): self { $this->prix = $prix; return $this; }
    public function getPlacesDisponibles(): ?int { return $this->placesDisponibles; }
    public function setPlacesDisponibles(int $placesDisponibles): self { $this->placesDisponibles = $placesDisponibles; return $this; }
    public function getChauffeur(): ?Utilisateur { return $this->chauffeur; }
    public function setChauffeur(?Utilisateur $chauffeur): self { $this->chauffeur = $chauffeur; return $this; }
    public function getVoiture(): ?Voiture { return $this->voiture; }
    public function setVoiture(?Voiture $voiture): self { $this->voiture = $voiture; return $this; }
    public function isEcologique(): bool { return $this->ecologique; }
    public function setEcologique(bool $ecologique): self { $this->ecologique = $ecologique; return $this; }
    public function hasIncident(): bool { return $this->incident; }
    public function setIncident(bool $incident): self { $this->incident = $incident; return $this; }
    public function getDescriptionIncident(): ?string { return $this->descriptionIncident; }
    public function setDescriptionIncident(?string $descriptionIncident): self { $this->descriptionIncident = $descriptionIncident; return $this; }
    public function isAnnule(): bool { return $this->annule; }
    public function setAnnule(bool $annule): self { $this->annule = $annule; return $this; }

    public function getParticipations(): Collection { return $this->participations; }
    public function addParticipation(Participation $participation): self {
        if (!$this->participations->contains($participation)) {
            $this->participations[] = $participation;
            $participation->setCovoiturage($this);
        }
        return $this;
    }
    public function removeParticipation(Participation $participation): self {
        if ($this->participations->removeElement($participation)) {
            if ($participation->getCovoiturage() === $this) {
                $participation->setCovoiturage(null);
            }
        }
        return $this;
    }

    public function getAvis(): Collection { return $this->avis; }
    public function addAvis(Avis $avis): self {
        if (!$this->avis->contains($avis)) {
            $this->avis[] = $avis;
            $avis->setCovoiturage($this);
        }
        return $this;
    }
    public function removeAvis(Avis $avis): self {
        if ($this->avis->removeElement($avis)) {
            if ($avis->getCovoiturage() === $this) {
                $avis->setCovoiturage(null);
            }
        }
        return $this;
    }

    #[ORM\Column(type: 'boolean')]
private bool $creditsAttribues = false;

public function isCreditsAttribues(): bool
{
    return $this->creditsAttribues;
}

public function setCreditsAttribues(bool $creditsAttribues): self
{
    $this->creditsAttribues = $creditsAttribues;
    return $this;
}

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }

    public function getStatut(): string
{
    return $this->statut;
}

public function setStatut(string $statut): self
{
    $this->statut = $statut;
    return $this;
}


public function isUserParticipant(Utilisateur $user): bool
{
    foreach ($this->participations as $participation) {
        if ($participation->getPassager() === $user && !$participation->isAnnule()) {
            return true;
        }
    }
    return false;
}

#[ORM\Column(type: 'datetime', nullable: true)]
private ?\DateTimeInterface $dateFin = null;

public function getDateFin(): ?\DateTimeInterface
{
    return $this->dateFin;
}

public function setDateFin(?\DateTimeInterface $dateFin): self
{
    $this->dateFin = $dateFin;
    return $this;
}

}
