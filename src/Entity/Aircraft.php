<?php

namespace App\Entity;

use App\Repository\AircraftRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AircraftRepository::class)]
class Aircraft
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $model = null;

    #[ORM\Column(length: 100)]
    private ?string $manufacturer = null;

    #[ORM\Column]
    private ?int $seating_capacity = null;

    #[ORM\Column]
    private ?int $max_range = null;

    #[ORM\Column(length: 100)]
    private ?string $engine_type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $first_flight_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): static
    {
        $this->model = $model;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(string $manufacturer): static
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    public function getSeatingCapacity(): ?int
    {
        return $this->seating_capacity;
    }

    public function setSeatingCapacity(int $seating_capacity): static
    {
        $this->seating_capacity = $seating_capacity;

        return $this;
    }

    public function getMaxRange(): ?int
    {
        return $this->max_range;
    }

    public function setMaxRange(int $max_range): static
    {
        $this->max_range = $max_range;

        return $this;
    }

    public function getEngineType(): ?string
    {
        return $this->engine_type;
    }

    public function setEngineType(string $engine_type): static
    {
        $this->engine_type = $engine_type;

        return $this;
    }

    public function getFirstFlightDate(): ?string
    {
        return $this->first_flight_date ? $this->first_flight_date->format('d M Y') : null;

    }

    public function setFirstFlightDate(?\DateTimeInterface $first_flight_date): static
    {
        $this->first_flight_date = $first_flight_date;

        return $this;
    }
}
