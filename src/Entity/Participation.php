<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    private $passager;

    #[ORM\ManyToOne(targetEntity: Covoiturage::class)]
    private $covoiturage;

    #[ORM\Column(type: 'boolean')]
    private $confirme = false;

    public function getId(): ?int { return $this->id; }
    public function getPassager(): ?Utilisateur { return $this->passager; }
    public function setPassager(?Utilisateur $passager): self { $this->passager = $passager; return $this; }
    public function getCovoiturage(): ?Covoiturage { return $this->covoiturage; }
    public function setCovoiturage(?Covoiturage $covoiturage): self { $this->covoiturage = $covoiturage; return $this; }
    public function isConfirme(): bool { return $this->confirme; }
    public function setConfirme(bool $confirme): self { $this->confirme = $confirme; return $this; }
}
