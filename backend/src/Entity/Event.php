<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTime $eventDate = null;

    #[ORM\Column(length: 255)]
    private ?string $location = null;

    #[ORM\Column(nullable: true)]
    private ?int $maxParticipants = null;

    #[ORM\Column]
    private ?bool $isPublic = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'attendingEvents')]
    private Collection $users;
    

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Group $eventGroup = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    private ?EventType $eventType = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->creator = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    public function getEventDate(): ?\DateTime
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTime $eventDate): static
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->maxParticipants;
    }

    public function setMaxParticipants(?int $maxParticipants): static
    {
        $this->maxParticipants = $maxParticipants;

        return $this;
    }

    public function isPublic(): ?bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): static
    {
        $this->isPublic = $isPublic;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addAttendingEvent($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeAttendingEvent($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCreator(): Collection
    {
        return $this->creator;
    }

    public function addOrganizer(User $organizer): static
    {
        if (!$this->creator->contains($organizer)) {
            $this->creator->add($organizer);
            $organizer->addOrganizedEvent($this);
        }

        return $this;
    }

    public function removeOrganizer(User $organizer): static
    {
        if ($this->creator->removeElement($organizer)) {
            $organizer->removeOrganizedEvent($this);
        }

        return $this;
    }

    public function getEventGroup(): ?Group
    {
        return $this->eventGroup;
    }

    public function setEventGroup(?Group $eventGroup): static
    {
        $this->eventGroup = $eventGroup;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getEventType(): ?EventType
    {
        return $this->eventType;
    }

    public function setEventType(?EventType $eventType): static
    {
        $this->eventType = $eventType;

        return $this;
    }
}
