<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(length: 50)]
    private $marque;

    #[ORM\Column(length: 50)]
    private $modele;

    #[ORM\Column(length: 50)]
    private $couleur;

    #[ORM\Column(length: 20)]
    private $energie; // électrique, essence

    #[ORM\Column(length: 20)]
    private $plaqueImmatriculation;

    #[ORM\Column(type: 'integer')]
    private $nbPlaces;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    private $proprietaire;

    public function getId(): ?int { return $this->id; }
    public function getMarque(): ?string { return $this->marque; }
    public function setMarque(string $marque): self { $this->marque = $marque; return $this; }
    public function getModele(): ?string { return $this->modele; }
    public function setModele(string $modele): self { $this->modele = $modele; return $this; }
    public function getCouleur(): ?string { return $this->couleur; }
    public function setCouleur(string $couleur): self { $this->couleur = $couleur; return $this; }
    public function getEnergie(): ?string { return $this->energie; }
    public function setEnergie(string $energie): self { $this->energie = $energie; return $this; }
    public function getPlaqueImmatriculation(): ?string { return $this->plaqueImmatriculation; }
    public function setPlaqueImmatriculation(string $plaqueImmatriculation): self { $this->plaqueImmatriculation = $plaqueImmatriculation; return $this; }
    public function getNbPlaces(): ?int { return $this->nbPlaces; }
    public function setNbPlaces(int $nbPlaces): self { $this->nbPlaces = $nbPlaces; return $this; }
    public function getProprietaire(): ?Utilisateur { return $this->proprietaire; }
    public function setProprietaire(?Utilisateur $proprietaire): self { $this->proprietaire = $proprietaire; return $this; }
}
