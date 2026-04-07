<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $startTime = null;

    #[ORM\Column]
    private ?\DateTime $endTime = null;

    #[ORM\Column]
    private ?int $maxAttendees = null;

    #[ORM\Column(length: 255)]
    private ?string $sessionType = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'sessions')]
    private ?conference $conference = null;

    /**
     * @var Collection<int, speaker>
     */
    #[ORM\ManyToMany(targetEntity: speaker::class, inversedBy: 'sessions')]
    private Collection $speaker;

    /**
     * @var Collection<int, room>
     */
    #[ORM\ManyToMany(targetEntity: room::class, inversedBy: 'sessions')]
    private Collection $room;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $track = null;

    #[ORM\Column(nullable: true)]
    private ?int $capacity = null;

    public function __construct()
    {
        $this->speaker = new ArrayCollection();
        $this->room = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartTime(): ?\DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTime $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTime $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getMaxAttendees(): ?int
    {
        return $this->maxAttendees;
    }

    public function setMaxAttendees(int $maxAttendees): static
    {
        $this->maxAttendees = $maxAttendees;

        return $this;
    }

    public function getSessionType(): ?string
    {
        return $this->sessionType;
    }

    public function setSessionType(string $sessionType): static
    {
        $this->sessionType = $sessionType;

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

    public function getConference(): ?conference
    {
        return $this->conference;
    }

    public function setConference(?conference $conference): static
    {
        $this->conference = $conference;

        return $this;
    }

    /**
     * @return Collection<int, speaker>
     */
    public function getSpeaker(): Collection
    {
        return $this->speaker;
    }

    public function addSpeaker(speaker $speaker): static
    {
        if (!$this->speaker->contains($speaker)) {
            $this->speaker->add($speaker);
        }

        return $this;
    }

    public function removeSpeaker(speaker $speaker): static
    {
        $this->speaker->removeElement($speaker);

        return $this;
    }

    /**
     * @return Collection<int, room>
     */
    public function getRoom(): Collection
    {
        return $this->room;
    }

    public function addRoom(room $room): static
    {
        if (!$this->room->contains($room)) {
            $this->room->add($room);
        }

        return $this;
    }

    public function removeRoom(room $room): static
    {
        $this->room->removeElement($room);

        return $this;
    }

    public function getTrack(): ?string
    {
        return $this->track;
    }

    public function setTrack(?string $track): static
    {
        $this->track = $track;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(?int $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
    }
}
