<?php

namespace App\Entity;

use App\Repository\SeasonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SeasonRepository::class)]
#[UniqueEntity(
    fields: ['number', 'movie'],
    errorPath: 'number',
    message: "Cette saison existe déjà pour cette série"
    )]
class Season
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank()]
    #[Assert\GreaterThan(-1)]
    private ?int $number = null;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotBlank()]
    #[Assert\GreaterThan(0)]
    private ?int $episodesNumber = null;

    #[ORM\ManyToOne(inversedBy: 'seasons')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank()]
    #[Assert\Type(Movie::class)]
    private ?Movie $movie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getEpisodesNumber(): ?int
    {
        return $this->episodesNumber;
    }

    public function setEpisodesNumber(int $episodesNumber): static
    {
        $this->episodesNumber = $episodesNumber;

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
}
