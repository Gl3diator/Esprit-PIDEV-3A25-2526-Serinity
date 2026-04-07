<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
 
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 150)]
    private ?string $fullName = null;
    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

 
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $speciality = null;
    #[ORM\Column(length: 150, nullable: true)]
    private ?string $password = null;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $createdAt;

    #[ORM\OneToMany(mappedBy: "patient", targetEntity: RendezVous::class)]
    private Collection $rendezVousPatient;

    #[ORM\OneToMany(mappedBy: "doctor", targetEntity: RendezVous::class)]
    private Collection $rendezVousDoctor;

    #[ORM\OneToMany(mappedBy: "doctor", targetEntity: Consultation::class)]
    private Collection $consultations;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $role = [];

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
    public function __construct()
    {
        $this->rendezVousPatient = new ArrayCollection();
        $this->rendezVousDoctor = new ArrayCollection();
        $this->consultations = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

 

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->role = $roles;

        return $this;
    }
 
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(?string $password): self { $this->password = $password; return $this; }
    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(string $fullName): self { $this->fullName = $fullName; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }

    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }

  
    public function getSpeciality(): ?string { return $this->speciality; }
    public function setSpeciality(?string $speciality): self { $this->speciality = $speciality; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }

    public function getRendezVousPatient(): Collection { return $this->rendezVousPatient; }
    public function getRendezVousDoctor(): Collection { return $this->rendezVousDoctor; }
    public function getConsultations(): Collection { return $this->consultations; }
}
