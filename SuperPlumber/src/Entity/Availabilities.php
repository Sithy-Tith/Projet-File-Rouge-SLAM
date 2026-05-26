<?php

namespace App\Entity;

use App\Entity\Employees;

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

    #[ORM\Column(type: Types::BOOLEAN)]
    private ?bool $all_day = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTime $end = null;

    #[ORM\ManyToOne(inversedBy: 'Availabilities')]
    private ?Employees $fkEmployee = null;

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

    public function isAllDay(): ?bool
    {
        return $this->all_day;
    }

    public function setAllDay(?bool $all_day): static
    {
        $this->all_day = $all_day;
        return $this;
    }
}
