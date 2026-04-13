<?php

namespace App\Entity;

use App\Enum\Status;
use App\Enum\Type;
use App\Repository\InterventionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionsRepository::class)]
class Interventions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: Type::class)]
    private Type $type;

    #[ORM\Column]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(enumType: Status::class)]
    private ?Status $status = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    private ?Employees $fkEmployee = null;

    #[ORM\ManyToOne(inversedBy: 'interventions')]
    private ?Clients $fkClient = null;

    /**
     * @var Collection<int, UsedPieces>
     */
    #[ORM\OneToMany(targetEntity: UsedPieces::class, mappedBy: 'fkIntervention', orphanRemoval: true)]
    private Collection $usedPieces;

    public function __construct()
    {
        $this->usedPieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(): ?Type
    {
        return $this->type;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

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

    public function getFkClient(): ?Clients
    {
        return $this->fkClient;
    }

    public function setFkClient(?Clients $fkClient): static
    {
        $this->fkClient = $fkClient;

        return $this;
    }

    /**
     * @return Collection<int, UsedPieces>
     */
    public function getUsedPieces(): Collection
    {
        return $this->usedPieces;
    }

    public function addUsedPiece(UsedPieces $usedPiece): static
    {
        if (!$this->usedPieces->contains($usedPiece)) {
            $this->usedPieces->add($usedPiece);
            $usedPiece->setFkIntervention($this);
        }

        return $this;
    }

    public function removeUsedPiece(UsedPieces $usedPiece): static
    {
        if ($this->usedPieces->removeElement($usedPiece)) {
            // set the owning side to null (unless already changed)
            if ($usedPiece->getFkIntervention() === $this) {
                $usedPiece->setFkIntervention(null);
            }
        }

        return $this;
    }
}
