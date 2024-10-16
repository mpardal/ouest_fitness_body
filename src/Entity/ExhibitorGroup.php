<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ExhibitorGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    public string $groupName;

    #[ORM\ManyToMany(targetEntity: Event::class, mappedBy: 'exhibitorGroups')]
    public iterable $events;  // Les événements auxquels ce groupe participe

    #[ORM\OneToMany(mappedBy: 'exhibitorGroup', targetEntity: Exhibitor::class)]
    public iterable $exhibitors;  // Les exposants appartenant à ce groupe

    #[ORM\OneToMany(mappedBy: 'exhibitorGroup', targetEntity: User::class)]
    public iterable $users;  // Les utilisateurs (exposants) associés à ce groupe

    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->exhibitors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function addEvent(Event $event): void
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addExhibitorGroup($this);
        }
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): ExhibitorGroup
    {
        $this->groupName = $groupName;

        return $this;
    }

    public function getEvents(): iterable
    {
        return $this->events;
    }

    public function setEvents(iterable $events): ExhibitorGroup
    {
        $this->events = $events;

        return $this;
    }

    public function getExhibitors(): iterable
    {
        return $this->exhibitors;
    }

    public function setExhibitors(iterable $exhibitors): ExhibitorGroup
    {
        $this->exhibitors = $exhibitors;

        return $this;
    }

    public function getUsers(): iterable
    {
        return $this->users;
    }

    public function setUsers(iterable $users): ExhibitorGroup
    {
        $this->users = $users;

        return $this;
    }
}