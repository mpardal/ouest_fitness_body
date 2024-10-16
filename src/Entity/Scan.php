<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Scan
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\ManyToOne(targetEntity: Ticket::class)]
    #[ORM\JoinColumn(nullable: false)]
    public Ticket $ticket;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public User $scannedBy;  // Le bénévole qui a scanné le billet

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $scannedAt;

    public function __construct()
    {
        $this->scannedAt = new \DateTime();
    }

    public function getTicket(): Ticket
    {
        return $this->ticket;
    }

    public function setTicket(Ticket $ticket): Scan
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getScannedBy(): User
    {
        return $this->scannedBy;
    }

    public function setScannedBy(User $scannedBy): Scan
    {
        $this->scannedBy = $scannedBy;

        return $this;
    }

    public function getScannedAt(): \DateTimeInterface
    {
        return $this->scannedAt;
    }

    public function setScannedAt(\DateTimeInterface $scannedAt): Scan
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }
}