<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity]
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

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {   
    $roles = $this->roles;
    $roles[] = 'ROLE_USER'; // toujours présent par défaut
    return array_unique($roles);
    }

    public function deductCredits(float $amount): bool
{
    if ($this->credits >= $amount) {
        $this->credits -= $amount;
        return true;
    }
    return false;
}

    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function getPseudo(): ?string { return $this->pseudo; }
    public function setPseudo(string $pseudo): self { $this->pseudo = $pseudo; return $this; }

    public function getCredits(): int { return $this->credits; }
    public function setCredits(int $credits): self { $this->credits = $credits; return $this; }

    public function getTelephone(): ?string { return $this->telephone; }
    public function setTelephone(?string $telephone): self { $this->telephone = $telephone; return $this; }

    public function getAdresse(): ?string { return $this->adresse; }
    public function setAdresse(?string $adresse): self { $this->adresse = $adresse; return $this; }

    public function eraseCredentials(): void
    {
    }
    
}


