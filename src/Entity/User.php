<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 180, unique: true)]
    public string $email;

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    public string $lastName;

    #[ORM\Column(length: 255)]
    public string $firstName;

    #[ORM\ManyToOne(targetEntity: ExhibitorGroup::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: true)]  // Un utilisateur peut ne pas être lié à un groupe
    public ?ExhibitorGroup $exhibitorGroup = null;

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';  // Chaque utilisateur a au moins le rôle 'ROLE_USER'
        return array_unique($roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function eraseCredentials(): void
    {
        // Si tu stockes des informations sensibles, tu peux les effacer ici
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): User
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): User
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getExhibitorGroup(): ?ExhibitorGroup
    {
        return $this->exhibitorGroup;
    }

    public function setExhibitorGroup(?ExhibitorGroup $exhibitorGroup): User
    {
        $this->exhibitorGroup = $exhibitorGroup;

        return $this;
    }
}