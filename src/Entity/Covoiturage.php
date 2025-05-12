<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    private $chauffeur;

    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    private $voiture;

    #[ORM\Column(type: 'boolean')]
    private $ecologique = false;

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
}
