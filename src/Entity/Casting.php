<?php

namespace App\Entity;

use App\Repository\CastingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: CastingRepository::class)]
#[UniqueEntity('castingOrder')]
class Casting
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $role = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\GreaterThan(0)]
    private ?int $castingOrder = null;

    #[ORM\ManyToOne(inversedBy: 'castings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\Type(Movie::class)]
    private ?Movie $movie = null;

    #[ORM\ManyToOne(inversedBy: 'castings')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\Type(Person::class)]
    private ?Person $person = null;
 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getCastingOrder(): ?int
    {
        return $this->castingOrder;
    }

    public function setCastingOrder(int $castingOrder): static
    {
        $this->castingOrder = $castingOrder;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

        return $this;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): static
    {
        $this->person = $person;

        return $this;
    }

}
