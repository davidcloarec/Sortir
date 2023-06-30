<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'La sortie doit avoir un nom')]
    #[Assert\Length(
        min:3, max: 255,
        minMessage: 'Le titre doit etre superieur à 3 caractères',
        maxMessage: 'Le titre doit etre inferieur à 255 caractères'
    )]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank(message: 'La date doit etre renseigné')]
    #[Assert\GreaterThanOrEqual(value: (new \DateTime()), message: 'la date doit etre superieur à la date du jour')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startingTime = null;


    #[Assert\Type(type: 'integer', message: 'la durée doit etre un nombre entier')]
    #[ORM\Column]
    private ?int $duration = null;


    #[Assert\Type(type: 'integer', message: 'le nombre max de participants doit etre un nombre entier')]
    #[Assert\Range(
        notInRangeMessage: 'le nombre de participant doit etre compris entre 1 et 100',
        min: 1,
        max: 100
    )]
    #[ORM\Column(nullable: true)]
    private ?int $maxSignUp = null;

    #[Assert\NotBlank(message: 'La date doit etre renseigné')]
    #[Assert\GreaterThan(value: (new \DateTime()), message: 'la date doit etre superieur à la date du jour')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $signUpLimit = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $info = null;

    #[Assert\NotBlank(message: 'La sortie doit avoir un campus')]
    #[ORM\ManyToOne(inversedBy: 'activity')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[Assert\NotBlank(message: 'La sortie doit avoir un lieu')]
    #[ORM\ManyToOne(inversedBy: 'activities')]
    private ?Venue $venue = null;

    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: 'activities')]
    private Collection $participants;

    #[ORM\ManyToOne(inversedBy: 'myActivities')]
    private ?Participant $organizer = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[ORM\JoinColumn(nullable: false)]
    private ?State $state = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $cancelMotive = null;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->campus; // Replace "name" with the property that represents the string representation of the activity.
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

    public function getStartingTime(): ?\DateTimeInterface
    {
        return $this->startingTime;
    }

    public function setStartingTime(\DateTimeInterface $startingTime): static
    {
        $this->startingTime = $startingTime;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMaxSignUp(): ?int
    {
        return $this->maxSignUp;
    }

    public function setMaxSignUp(?int $maxSignUp): static
    {
        $this->maxSignUp = $maxSignUp;

        return $this;
    }

    public function getSignUpLimit(): ?\DateTimeInterface
    {
        return $this->signUpLimit;
    }

    public function setSignUpLimit(\DateTimeInterface $signUpLimit): static
    {
        $this->signUpLimit = $signUpLimit;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(?string $info): static
    {
        $this->info = $info;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): static
    {
        $this->campus = $campus;

        return $this;
    }

    public function getVenue(): ?Venue
    {
        return $this->venue;
    }

    public function setVenue(?Venue $venue): static
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): static
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->addActivity($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): static
    {
        if ($this->participants->removeElement($participant)) {
            $participant->removeActivity($this);
        }

        return $this;
    }

    public function getOrganizer(): ?Participant
    {
        return $this->organizer;
    }

    public function setOrganizer(?Participant $organizer): static
    {
        $this->organizer = $organizer;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): static
    {
        $this->state = $state;

        return $this;
    }

    public function getCancelMotive(): ?string
    {
        return $this->cancelMotive;
    }

    public function setCancelMotive(?string $cancelMotive): static
    {
        $this->cancelMotive = $cancelMotive;

        return $this;
    }
}
