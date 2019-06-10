<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MareeRestrictionRepository")
 */
class MareeRestriction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMax;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMin;


    /**
     * @ORM\ManyToOne(targetEntity="Spot", inversedBy="mareeRestriction", cascade={"persist"})
     * @ORM\JoinColumn(name="spot_id", referencedColumnName="id")
     */
    private $spot;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     * "OK", "KO", "warn"
     */
    private $state;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHauteurMax()
    {
        return $this->hauteurMax;
    }

    public function setHauteurMax($hauteurMax): self
    {
        $this->hauteurMax = $hauteurMax;

        return $this;
    }

    public function getHauteurMin()
    {
        return $this->hauteurMin;
    }

    public function setHauteurMin($hauteurMin): self
    {
        $this->hauteurMin = $hauteurMin;

        return $this;
    }

    public function setSpot(Spot $spot = null)
    {
        $this->spot = $spot;

        return $this;
    }

    public function getSpot() : Spot
    {
        return $this->spot;
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
}
