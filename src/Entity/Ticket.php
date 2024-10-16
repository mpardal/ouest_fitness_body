<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255, unique: true)]
    public string $qrCode;

    #[ORM\Column(type: 'boolean')]
    public bool $isValidated = false;  // Indique si le billet a été scanné ou non

    #[ORM\ManyToOne(targetEntity: Participant::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    public Participant $participant;

    #[ORM\ManyToOne(targetEntity: Event::class)]
    #[ORM\JoinColumn(nullable: false)]
    public Event $event;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    public Order $order;

    public function getQrCode(): string
    {
        return $this->qrCode;
    }

    public function setQrCode(string $qrCode): Ticket
    {
        $this->qrCode = $qrCode;

        return $this;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): Ticket
    {
        $this->isValidated = $isValidated;

        return $this;
    }

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function setParticipant(Participant $participant): Ticket
    {
        $this->participant = $participant;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): Ticket
    {
        $this->event = $event;

        return $this;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): Ticket
    {
        $this->order = $order;

        return $this;
    }
}