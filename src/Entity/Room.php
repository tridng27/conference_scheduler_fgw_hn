<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(length: 255)]
    private ?string $building = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'room')]
    private Collection $sessions;

    /**
     * @var Collection<int, venue>
     */
    #[ORM\ManyToMany(targetEntity: venue::class, inversedBy: 'rooms')]
    private Collection $venue;

    #[ORM\Column(length: 50)]
    private ?string $floor = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $equipment = null;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
        $this->venue = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getBuilding(): ?string
    {
        return $this->building;
    }

    public function setBuilding(string $building): static
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addRoom($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            $session->removeRoom($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, venue>
     */
    public function getVenue(): Collection
    {
        return $this->venue;
    }

    public function addVenue(venue $venue): static
    {
        if (!$this->venue->contains($venue)) {
            $this->venue->add($venue);
        }

        return $this;
    }

    public function removeVenue(venue $venue): static
    {
        $this->venue->removeElement($venue);

        return $this;
    }

    public function getFloor(): ?string
    {
        return $this->floor;
    }

    public function setFloor(string $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function getEquipment(): ?string
    {
        return $this->equipment;
    }

    public function setEquipment(?string $equipment): static
    {
        $this->equipment = $equipment;

        return $this;
    }
}
