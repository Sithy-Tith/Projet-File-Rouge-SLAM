<?php

namespace App\Entity;

use App\Repository\UsedPiecesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsedPiecesRepository::class)]
class UsedPieces
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Interventions>
     */
    #[ORM\ManyToMany(targetEntity: Interventions::class, inversedBy: 'usedPieces')]
    private Collection $fkIntervention;

    /**
     * @var Collection<int, Pieces>
     */
    #[ORM\ManyToMany(targetEntity: Pieces::class, inversedBy: 'usedPieces')]
    private Collection $fkPiece;

    #[ORM\Column]
    private ?bool $isConsumable = null;

    public function __construct()
    {
        $this->fkIntervention = new ArrayCollection();
        $this->fkPiece = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Interventions>
     */
    public function getFkIntervention(): Collection
    {
        return $this->fkIntervention;
    }

    public function addFkIntervention(Interventions $fkIntervention): static
    {
        if (!$this->fkIntervention->contains($fkIntervention)) {
            $this->fkIntervention->add($fkIntervention);
        }

        return $this;
    }

    public function removeFkIntervention(Interventions $fkIntervention): static
    {
        $this->fkIntervention->removeElement($fkIntervention);

        return $this;
    }

    /**
     * @return Collection<int, Pieces>
     */
    public function getFkPiece(): Collection
    {
        return $this->fkPiece;
    }

    public function addFkPiece(Pieces $fkPiece): static
    {
        if (!$this->fkPiece->contains($fkPiece)) {
            $this->fkPiece->add($fkPiece);
        }

        return $this;
    }

    public function removeFkPiece(Pieces $fkPiece): static
    {
        $this->fkPiece->removeElement($fkPiece);

        return $this;
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
}
