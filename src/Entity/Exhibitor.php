<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Exhibitor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public readonly int $id;

    #[ORM\Column(length: 255)]
    public string $companyName;

    #[ORM\Column(type: 'text')]
    public string $description;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    public ?string $website = null;

    #[ORM\ManyToOne(targetEntity: ExhibitorGroup::class, inversedBy: 'exhibitors')]
    #[ORM\JoinColumn(nullable: false)]
    public ExhibitorGroup $exhibitorGroup;

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): Exhibitor
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): Exhibitor
    {
        $this->description = $description;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): Exhibitor
    {
        $this->website = $website;

        return $this;
    }

    public function getExhibitorGroup(): ExhibitorGroup
    {
        return $this->exhibitorGroup;
    }

    public function setExhibitorGroup(ExhibitorGroup $exhibitorGroup): Exhibitor
    {
        $this->exhibitorGroup = $exhibitorGroup;

        return $this;
    }
}