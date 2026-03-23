<?php

namespace App\Entity;

use App\Repository\AvailibilitiesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailibilitiesRepository::class)]
class Availibilities
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $availibility = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date = null;

    #[ORM\ManyToOne(inversedBy: 'availibilities')]
    private ?Employees $fkEmployee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvailibility(): ?int
    {
        return $this->availibility;
    }

    public function setAvailibility(int $availibility): static
    {
        $this->availibility = $availibility;

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
