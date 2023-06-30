<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Venue::class)]
    private Collection $venue;

    public function __construct()
    {
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

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return Collection<int, Venue>
     */
    public function getVenue(): Collection
    {
        return $this->venue;
    }

    public function addVenue(Venue $venue): static
    {
        if (!$this->venue->contains($venue)) {
            $this->venue->add($venue);
            $venue->setCity($this);
        }

        return $this;
    }

    public function removeVenue(Venue $venue): static
    {
        if ($this->venue->removeElement($venue)) {
            // set the owning side to null (unless already changed)
            if ($venue->getCity() === $this) {
                $venue->setCity(null);
            }
        }

        return $this;
    }
}