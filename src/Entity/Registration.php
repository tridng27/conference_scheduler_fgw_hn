<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $registrationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    /**
     * @var Collection<int, user>
     */
    #[ORM\ManyToMany(targetEntity: user::class, inversedBy: 'registrations')]
    private Collection $usser;

    /**
     * @var Collection<int, conference>
     */
    #[ORM\ManyToMany(targetEntity: conference::class, inversedBy: 'registrations')]
    private Collection $conference;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $ticketType = null;

    public function __construct()
    {
        $this->usser = new ArrayCollection();
        $this->conference = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistrationDate(): ?\DateTime
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTime $registrationDate): static
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUsser(): Collection
    {
        return $this->usser;
    }

    public function addUsser(user $usser): static
    {
        if (!$this->usser->contains($usser)) {
            $this->usser->add($usser);
        }

        return $this;
    }

    public function removeUsser(user $usser): static
    {
        $this->usser->removeElement($usser);

        return $this;
    }

    /**
     * @return Collection<int, conference>
     */
    public function getConference(): Collection
    {
        return $this->conference;
    }

    public function addConference(conference $conference): static
    {
        if (!$this->conference->contains($conference)) {
            $this->conference->add($conference);
        }

        return $this;
    }

    public function removeConference(conference $conference): static
    {
        $this->conference->removeElement($conference);

        return $this;
    }

    public function getTicketType(): ?string
    {
        return $this->ticketType;
    }

    public function setTicketType(?string $ticketType): static
    {
        $this->ticketType = $ticketType;

        return $this;
    }
}
