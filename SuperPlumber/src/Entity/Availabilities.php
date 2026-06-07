<?php

namespace App\Entity;

use App\Entity\Employees;

use App\Repository\AvailabilitiesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AvailabilitiesRepository::class)]
class Availabilities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $end = null;

    #[ORM\ManyToOne(inversedBy: 'Availabilities')]
    private ?Employees $fkEmployee = null;

    #[ORM\OneToMany(targetEntity: Interventions::class, mappedBy: 'fkAvailability')]
    private Collection $interventions;

    public function __construct()
    {
        $this->interventions = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTime
    {
        return $this->start;
    }

    public function setStart(\DateTime $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(\DateTime $end): static
    {
        $this->end = $end;

        return $this;
    }

    public function getFkEmployee(): ?Employees
    {
        return $this->fkEmployee;
    }

    public function setFkEmployee(?Employees $fkEmployee): static
    {
        $this->fkEmployee = $fkEmployee;

        return $this;
    }

    public function getInterventions(): Collection
    {
        return $this->interventions;
    }
}
