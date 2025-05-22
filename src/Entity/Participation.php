<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipationRepository;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'participations')]
    private $passager;

    #[ORM\ManyToOne(targetEntity: Covoiturage::class, inversedBy: 'participations')]
    private $covoiturage;

    #[ORM\Column(type: 'boolean')]
    private $confirme = false;

    #[ORM\Column(type: 'datetime')]
    private $dateParticipation;

    #[ORM\Column(type: 'boolean')]
    private $annule = false;

    public function __construct()
    {
        $this->dateParticipation = new \DateTime();
    }

#[ORM\Column(type: 'boolean')]
private $validationTrajet = false;

public function isValidationTrajet(): bool
{
    return $this->validationTrajet;
}

public function setValidationTrajet(bool $validationTrajet): self
{
    $this->validationTrajet = $validationTrajet;
    return $this;
}

#[ORM\Column(type: 'text', nullable: true)]
private $commentaireProbleme;

public function getCommentaireProbleme(): ?string
{
    return $this->commentaireProbleme;
}

public function setCommentaireProbleme(?string $commentaireProbleme): self
{
    $this->commentaireProbleme = $commentaireProbleme;
    return $this;
}

    public function getId(): ?int { return $this->id; }

    public function getPassager(): ?Utilisateur { return $this->passager; }
    public function setPassager(?Utilisateur $passager): self { $this->passager = $passager; return $this; }

    public function getCovoiturage(): ?Covoiturage { return $this->covoiturage; }
    public function setCovoiturage(?Covoiturage $covoiturage): self { $this->covoiturage = $covoiturage; return $this; }

    public function isConfirme(): bool { return $this->confirme; }
    public function setConfirme(bool $confirme): self { $this->confirme = $confirme; return $this; }

    public function getDateParticipation(): ?\DateTimeInterface { return $this->dateParticipation; }
    public function setDateParticipation(\DateTimeInterface $dateParticipation): self {
        $this->dateParticipation = $dateParticipation;
        return $this;
    }

    public function isAnnule(): bool { return $this->annule; }
    public function setAnnule(bool $annule): self { $this->annule = $annule; return $this; }
}
