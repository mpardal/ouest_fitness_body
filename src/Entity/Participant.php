<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Participant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Column(length: 255)]
    public string $email;

    #[ORM\OneToMany(mappedBy: 'participant', targetEntity: Ticket::class)]
    public iterable $tickets;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Participant
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): Participant
    {
        $this->email = $email;

        return $this;
    }

    public function getTickets(): iterable
    {
        return $this->tickets;
    }

    public function setTickets(iterable $tickets): Participant
    {
        $this->tickets = $tickets;

        return $this;
    }
}