<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpotRepository")
 */
class Spot
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_route;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_maree;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $time_from_paris;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $km_from_paris;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $km_autoroute_from_paris;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price_autoroute_from_paris;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $gps_lat;

    /**
     * @ORM\Column(type="decimal", nullable=true)
     */
    private $gps_long;




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug() : string
    {
        return (new Slugify())->slugify($this->name);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescRoute(): ?string
    {
        return $this->desc_route;
    }

    public function setDescRoute(?string $desc_route): self
    {
        $this->desc_route = $desc_route;

        return $this;
    }

    public function getDescMaree(): ?string
    {
        return $this->desc_maree;
    }

    public function setDescMaree(?string $desc_maree): self
    {
        $this->desc_maree = $desc_maree;

        return $this;
    }

    public function getTimeFromParis(): ?int
    {
        return $this->time_from_paris;
    }

    public function setTimeFromParis(?int $time_from_paris): self
    {
        $this->time_from_paris = $time_from_paris;

        return $this;
    }

    public function getKmFromParis(): ?int
    {
        return $this->km_from_paris;
    }

    public function setKmFromParis(?int $km_from_paris): self
    {
        $this->km_from_paris = $km_from_paris;

        return $this;
    }

    public function getKmAutorouteFromParis(): ?int
    {
        return $this->km_autoroute_from_paris;
    }

    public function setKmAutorouteFromParis(?int $km_autoroute_from_paris): self
    {
        $this->km_autoroute_from_paris = $km_autoroute_from_paris;

        return $this;
    }

    public function getPriceAutorouteFromParis(): ?int
    {
        return $this->price_autoroute_from_paris;
    }

    public function setPriceAutorouteFromParis(?int $price_autoroute_from_paris): self
    {
        $this->price_autoroute_from_paris = $price_autoroute_from_paris;

        return $this;
    }

    public function getGpsLat(): ?int
    {
        return $this->gps_lat;
    }

    public function setGpsLat(?int $gps_lat): self
    {
        $this->gps_lat = $gps_lat;

        return $this;
    }

    public function getGpsLong(): ?int
    {
        return $this->gps_long;
    }

    public function setGpsLong(?int $gps_long): self
    {
        $this->gps_long = $gps_long;

        return $this;
    }
}
