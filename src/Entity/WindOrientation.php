<?php

namespace App\Entity;

use App\Utils\WebsiteGetData;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\WindOrientationRepository")
 */
class WindOrientation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $orientation;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $orientationDeg;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity="Spot", inversedBy="windOrientation")
     */
    private $spot;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(?string $orientation): self
    {
        $this->orientation = $orientation;
        $this->orientationDeg=WebsiteGetData::transformeOrientationDeg($orientation);

        return $this;
    }

    public function getOrientationDeg()
    {
        return $this->orientationDeg;
    }

    public function setOrientationDeg($orientationDeg): self
    {
        $this->orientationDeg = $orientationDeg;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getSpot(): ?Spot
    {
        return $this->spot;
    }

    public function setSpot(?Spot $spot): self
    {
        $this->spot = $spot;

        return $this;
    }
}
