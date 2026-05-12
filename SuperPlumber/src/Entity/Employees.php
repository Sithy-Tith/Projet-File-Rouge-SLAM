<?php

namespace App\Entity;

use App\Enum\Position;
use App\Repository\EmployeesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: EmployeesRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class Employees implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 45)]
    private ?string $lastName = null;

    #[ORM\Column(length: 45)]
    private ?string $firstName = null;

    #[ORM\Column(length: 45, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', enumType: Position::class)]
    private ?Position $position = null;

    #[ORM\OneToMany(targetEntity: Interventions::class, mappedBy: 'fkEmployee')]
    private Collection $interventions;

    #[ORM\OneToMany(targetEntity: Availabilities::class, mappedBy: 'fkEmployee')]
    private Collection $availabilities;

    public function __construct()
    {
        $this->interventions  = new ArrayCollection();
        $this->availabilities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array //getRoles modifié pour qu'il soit géré selon la position
    {
        return match ($this->position) {
            Position::ADMINISTRATOR => ['ROLE_ADMIN', 'ROLE_EMPLOYEE', 'ROLE_USER'],
            Position::PLUMBER       => ['ROLE_PLUMBER', 'ROLE_EMPLOYEE', 'ROLE_USER'],
            default                 => ['ROLE_EMPLOYEE', 'ROLE_USER'],
        };
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Pas de données sensibles temporaires à effacer
    }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        return $data;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function getInterventions(): Collection
    {
        return $this->interventions;
    }

    public function addIntervention(Interventions $intervention): static
    {
        if (!$this->interventions->contains($intervention)) {
            $this->interventions->add($intervention);
            $intervention->setFkEmployee($this);
        }
        return $this;
    }

    public function removeIntervention(Interventions $intervention): static
    {
        if ($this->interventions->removeElement($intervention)) {
            if ($intervention->getFkEmployee() === $this) {
                $intervention->setFkEmployee(null);
            }
        }
        return $this;
    }

    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(Availabilities $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setFkEmployee($this);
        }
        return $this;
    }

    public function removeAvailability(Availabilities $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            if ($availability->getFkEmployee() === $this) {
                $availability->setFkEmployee(null);
            }
        }
        return $this;
    }

    // -----------  Custom methods  -------------------

    // Return fullname on this syntax : "DUPONT Jean-Paul"
    public function getFullName(): String
    {

        $lastName = strtoupper($this->lastName);            // DUPONT
        $firstName = ucwords(strtolower($this->firstName)); // Jean-Paul

        return $lastName . ' ' . $firstName;
    }
}
