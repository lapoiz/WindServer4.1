<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
 * @UniqueEntity("codeRegion")
 */
class Region
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numDisplay;

    /**
     * @ORM\OneToMany(targetEntity="Spot", mappedBy="region")
     */
    private $spots;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $codeRegion;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->spots = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
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

    public function getNumDisplay(): ?int
    {
        return $this->numDisplay;
    }

    public function setNumDisplay(?int $numDisplay): self
    {
        $this->numDisplay = $numDisplay;

        return $this;
    }

    public function addSpot(Spot $spot)
    {
        $this->spots[] = $spot;

        return $this;
    }

    public function removeSpot(Spot $spot)
    {
        $this->spots->removeElement($spot);
    }

    public function getSpots()
    {
        return $this->spots;
    }

    public function getCodeRegion(): ?string
    {
        return $this->codeRegion;
    }

    public function setCodeRegion(string $codeRegion): self
    {
        $this->codeRegion = $codeRegion;

        return $this;
    }
}
