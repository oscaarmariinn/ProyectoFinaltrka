<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotNull]
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var ?string The hashed password
     */
    #[Assert\NotNull]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotNull]
    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotNull]
    private ?string $surname = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\DateTime]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $active = true;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users')]
    private Collection $userGroups;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'users')]
    private Collection $attendingEvents;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'responsibles')]
    private Collection $responsibleGroups;

    /**
     * @var Collection<int, Group>
     */
    #[ORM\OneToMany(targetEntity: Group::class, mappedBy: 'creator')]
    private Collection $createdGroups;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'creator')]
    private Collection $createdEvents;

    public function __construct()
    {
        $this->userGroups = new ArrayCollection();
        $this->attendingEvents = new ArrayCollection();
        $this->responsibleGroups = new ArrayCollection();
        $this->createdGroups = new ArrayCollection();
        $this->createdEvents = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array)$this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): static
    {
        $this->surname = $surname;

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

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getUserGroups(): Collection
    {
        return $this->userGroups;
    }

    public function addUserGroup(Group $userGroup): static
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups->add($userGroup);
        }

        return $this;
    }

    public function removeUserGroup(Group $userGroup): static
    {
        $this->userGroups->removeElement($userGroup);

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getAttendingEvents(): Collection
    {
        return $this->attendingEvents;
    }

    public function addAttendingEvent(Event $attendingEvent): static
    {
        if (!$this->attendingEvents->contains($attendingEvent)) {
            $this->attendingEvents->add($attendingEvent);
        }

        return $this;
    }

    public function removeAttendingEvent(Event $attendingEvent): static
    {
        $this->attendingEvents->removeElement($attendingEvent);

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getResponsibleGroups(): Collection
    {
        return $this->responsibleGroups;
    }

    public function addResponsibleGroup(Group $responsibleGroup): static
    {
        if (!$this->responsibleGroups->contains($responsibleGroup)) {
            $this->responsibleGroups->add($responsibleGroup);
            $responsibleGroup->addResponsible($this);
        }

        return $this;
    }

    public function removeResponsibleGroup(Group $responsibleGroup): static
    {
        if ($this->responsibleGroups->removeElement($responsibleGroup)) {
            $responsibleGroup->removeResponsible($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Group>
     */
    public function getCreatedGroups(): Collection
    {
        return $this->createdGroups;
    }

    public function addCreatedGroup(Group $createdGroup): static
    {
        if (!$this->createdGroups->contains($createdGroup)) {
            $this->createdGroups->add($createdGroup);
            $createdGroup->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedGroup(Group $createdGroup): static
    {
        if ($this->createdGroups->removeElement($createdGroup)) {
            // set the owning side to null (unless already changed)
            if ($createdGroup->getCreator() === $this) {
                $createdGroup->setCreator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getCreatedEvents(): Collection
    {
        return $this->createdEvents;
    }

    public function addCreatedEvent(Event $createdEvent): static
    {
        if (!$this->createdEvents->contains($createdEvent)) {
            $this->createdEvents->add($createdEvent);
            $createdEvent->setCreator($this);
        }

        return $this;
    }

    public function removeCreatedEvent(Event $createdEvent): static
    {
        if ($this->createdEvents->removeElement($createdEvent)) {
            // set the owning side to null (unless already changed)
            if ($createdEvent->getCreator() === $this) {
                $createdEvent->setCreator(null);
            }
        }

        return $this;
    }
}
