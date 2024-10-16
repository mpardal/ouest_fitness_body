<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(type: 'string', length: 255)]
    public string $buyerEmail;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: Ticket::class, cascade: ['persist', 'remove'])]
    public iterable $tickets;

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $orderDate;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    public float $totalPrice;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    public User $user;  // L'utilisateur qui a passé la commande

    public function __construct()
    {
        $this->orderDate = new \DateTime();
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addTicket(Ticket $ticket): void
    {
        $this->tickets[] = $ticket;
        $ticket->setOrder($this);
    }

    public function getBuyerEmail(): string
    {
        return $this->buyerEmail;
    }

    public function setBuyerEmail(string $buyerEmail): Order
    {
        $this->buyerEmail = $buyerEmail;

        return $this;
    }

    public function getTickets(): iterable
    {
        return $this->tickets;
    }

    public function setTickets(iterable $tickets): Order
    {
        $this->tickets = $tickets;

        return $this;
    }

    public function getOrderDate(): \DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): Order
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): Order
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Order
    {
        $this->user = $user;

        return $this;
    }
}