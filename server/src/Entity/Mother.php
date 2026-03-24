<?php

namespace App\Entity;

use App\Repository\MotherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MotherRepository::class)]
class Mother
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Kid>
     */
    #[ORM\OneToMany(targetEntity: Kid::class, mappedBy: 'mother')]
    private Collection $kids;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $birthyear = null;

    public function __construct()
    {
        $this->kids = new ArrayCollection();
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

    /**
     * @return Collection<int, Kid>
     */
    public function getKids(): Collection
    {
        return $this->kids;
    }

    public function addKid(Kid $kid): static
    {
        if (!$this->kids->contains($kid)) {
            $this->kids->add($kid);
            $kid->setMother($this);
        }

        return $this;
    }

    public function removeKid(Kid $kid): static
    {
        if ($this->kids->removeElement($kid)) {
            // set the owning side to null (unless already changed)
            if ($kid->getMother() === $this) {
                $kid->setMother(null);
            }
        }

        return $this;
    }

    public function getBirthyear(): ?\DateTime
    {
        return $this->birthyear;
    }

    public function setBirthyear(\DateTime $birthyear): static
    {
        $this->birthyear = $birthyear;

        return $this;
    }
}
