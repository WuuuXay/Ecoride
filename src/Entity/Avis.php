<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: "App\Repository\AvisRepository")]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $note;

    #[ORM\Column(type: 'text')]
    private $commentaire;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'avisDonnes')]
    private $auteur;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'avisRecus')]
    private $cible;

#[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: 'avis')]
private $covoiturage;

public function getCovoiturage(): ?Covoiturage { return $this->covoiturage; }
public function setCovoiturage(?Covoiturage $covoiturage): self { 
    $this->covoiturage = $covoiturage; 
    return $this; 
}

    

    #[ORM\Column(type: 'boolean')]
    private $valide = false;

    #[ORM\Column(type: 'datetime')]
    private $dateCreation;

    public function __construct()
    {
        $this->dateCreation = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }
    public function getNote(): ?int { return $this->note; }
    public function setNote(int $note): self { $this->note = $note; return $this; }
    public function getCommentaire(): ?string { return $this->commentaire; }
    public function setCommentaire(string $commentaire): self { $this->commentaire = $commentaire; return $this; }
    public function getAuteur(): ?Utilisateur { return $this->auteur; }
    public function setAuteur(?Utilisateur $auteur): self { $this->auteur = $auteur; return $this; }
    public function getCible(): ?Utilisateur { return $this->cible; }
    public function setCible(?Utilisateur $cible): self { $this->cible = $cible; return $this; }
    public function isValide(): bool { return $this->valide; }
    public function setValide(bool $valide): self { $this->valide = $valide; return $this; }
    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): self { $this->dateCreation = $dateCreation; return $this; }
}