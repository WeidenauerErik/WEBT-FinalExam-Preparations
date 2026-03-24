<?php

namespace App\Entity;

use App\Repository\KidRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: KidRepository::class)]
class Kid
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['kid:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['kid:read', 'kid:create'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['kid:read', 'kid:create'])]
    private ?\DateTime $birthyear = null;

    #[ORM\ManyToOne(inversedBy: 'kids')]
    #[Groups(['kid:read', 'kid:create'])]
    private ?Mother $mother = null;

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

    public function getBirthyear(): ?\DateTime
    {
        return $this->birthyear;
    }

    public function setBirthyear(\DateTime $birthyear): static
    {
        $this->birthyear = $birthyear;

        return $this;
    }

    public function getMother(): ?Mother
    {
        return $this->mother;
    }

    public function setMother(?Mother $mother): static
    {
        $this->mother = $mother;

        return $this;
    }
}
