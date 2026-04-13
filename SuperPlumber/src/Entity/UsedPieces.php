<?php

namespace App\Entity;

use App\Repository\UsedPiecesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsedPiecesRepository::class)]
class UsedPieces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $isConsumable = null;

    #[ORM\ManyToOne(inversedBy: 'usedPieces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Interventions $fkIntervention = null;

    #[ORM\ManyToOne(inversedBy: 'usedPieces')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pieces $fkPiece = null;

    #[ORM\Column]
    private ?float $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isConsumable(): ?bool
    {
        return $this->isConsumable;
    }

    public function setIsConsumable(bool $isConsumable): static
    {
        $this->isConsumable = $isConsumable;

        return $this;
    }

    public function getFkIntervention(): ?Interventions
    {
        return $this->fkIntervention;
    }

    public function setFkIntervention(?Interventions $fkIntervention): static
    {
        $this->fkIntervention = $fkIntervention;

        return $this;
    }

    public function getFkPiece(): ?Pieces
    {
        return $this->fkPiece;
    }

    public function setFkPiece(?Pieces $fkPiece): static
    {
        $this->fkPiece = $fkPiece;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
