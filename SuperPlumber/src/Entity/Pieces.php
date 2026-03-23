<?php

namespace App\Entity;

use App\Repository\PiecesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PiecesRepository::class)]
class Pieces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 45)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column]
    private ?int $alertTreshold = null;

    #[ORM\Column(length: 45)]
    private ?string $supplier = null;

    /**
     * @var Collection<int, UsedPieces>
     */
    #[ORM\ManyToMany(targetEntity: UsedPieces::class, mappedBy: 'fkPiece')]
    private Collection $usedPieces;

    public function __construct()
    {
        $this->usedPieces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAlertTreshold(): ?int
    {
        return $this->alertTreshold;
    }

    public function setAlertTreshold(int $alertTreshold): static
    {
        $this->alertTreshold = $alertTreshold;

        return $this;
    }

    public function getSupplier(): ?string
    {
        return $this->supplier;
    }

    public function setSupplier(string $supplier): static
    {
        $this->supplier = $supplier;

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
            $usedPiece->addFkPiece($this);
        }

        return $this;
    }

    public function removeUsedPiece(UsedPieces $usedPiece): static
    {
        if ($this->usedPieces->removeElement($usedPiece)) {
            $usedPiece->removeFkPiece($this);
        }

        return $this;
    }
}
