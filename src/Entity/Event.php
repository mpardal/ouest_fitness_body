<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    public string $title;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime')]
    public \DateTimeInterface $endDate;

    #[ORM\ManyToMany(targetEntity: ExhibitorGroup::class, inversedBy: 'events')]
    #[ORM\JoinTable(name: 'event_exhibitor_group')]
    public iterable $exhibitorGroups;  // Les groupes d'exposants participant à cet événement

    public function __construct()
    {
        $this->exhibitorGroups = new ArrayCollection();
    }

    public function addExhibitorGroup(ExhibitorGroup $group): void
    {
        if (!$this->exhibitorGroups->contains($group)) {
            $this->exhibitorGroups[] = $group;
        }
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Event
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Event
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): Event
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): Event
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getExhibitorGroups(): iterable
    {
        return $this->exhibitorGroups;
    }

    public function setExhibitorGroups(iterable $exhibitorGroups): Event
    {
        $this->exhibitorGroups = $exhibitorGroups;

        return $this;
    }
}