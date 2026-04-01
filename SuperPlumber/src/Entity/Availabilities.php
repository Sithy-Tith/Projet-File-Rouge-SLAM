<?php

namespace App\Entity;

use App\Repository\AvailabilitiesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilitiesRepository::class)]
class Availabilities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $availability = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'Availabilities')]
    private ?Employees $fkEmployee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getavailability(): ?int
    {
        return $this->availability;
    }

    public function setavailability(int $availability): static
    {
        $this->availability = $availability;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

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
}
