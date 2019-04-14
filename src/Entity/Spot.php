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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $desc_orientation_vent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlWindFinder;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLWindguru;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLMeteoFrance;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLMeteoConsult;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLMerteo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLMaree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLTempWater;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maree_OK;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maree_warn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $maree_KO;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMBGrandeMaree;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMHGrandeMaree;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMBMoyenneMaree;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMHMoyenneMaree;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMBPetiteMaree;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=1, nullable=true)
     */
    private $hauteurMHPetiteMaree;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlBalise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlWebcam;


    /**
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="spots")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id", nullable=true)
     */
    private $region;


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

    public function getDescOrientationVent(): ?string
    {
        return $this->desc_orientation_vent;
    }

    public function setDescOrientationVent(?string $desc_orientation_vent): self
    {
        $this->desc_orientation_vent = $desc_orientation_vent;

        return $this;
    }

    public function getUrlWindFinder(): ?string
    {
        return $this->urlWindFinder;
    }

    public function setUrlWindFinder(?string $urlWindFinder): self
    {
        $this->urlWindFinder = $urlWindFinder;

        return $this;
    }

    public function getURLWindguru(): ?string
    {
        return $this->URLWindguru;
    }

    public function setURLWindguru(?string $URLWindguru): self
    {
        $this->URLWindguru = $URLWindguru;

        return $this;
    }

    public function getURLMeteoFrance(): ?string
    {
        return $this->URLMeteoFrance;
    }

    public function setURLMeteoFrance(?string $URLMeteoFrance): self
    {
        $this->URLMeteoFrance = $URLMeteoFrance;

        return $this;
    }

    public function getURLMeteoConsult(): ?string
    {
        return $this->URLMeteoConsult;
    }

    public function setURLMeteoConsult(?string $URLMeteoConsult): self
    {
        $this->URLMeteoConsult = $URLMeteoConsult;

        return $this;
    }

    public function getURLMerteo(): ?string
    {
        return $this->URLMerteo;
    }

    public function setURLMerteo(?string $URLMerteo): self
    {
        $this->URLMerteo = $URLMerteo;

        return $this;
    }

    public function getURLMaree(): ?string
    {
        return $this->URLMaree;
    }

    public function setURLMaree(?string $URLMaree): self
    {
        $this->URLMaree = $URLMaree;

        return $this;
    }

    public function getURLTempWater(): ?string
    {
        return $this->URLTempWater;
    }

    public function setURLTempWater(?string $URLTempWater): self
    {
        $this->URLTempWater = $URLTempWater;

        return $this;
    }

    public function getMareeOK(): ?string
    {
        return $this->maree_OK;
    }

    public function setMareeOK(?string $maree_OK): self
    {
        $this->maree_OK = $maree_OK;

        return $this;
    }

    public function getMareeWarn(): ?string
    {
        return $this->maree_warn;
    }

    public function setMareeWarn(?string $maree_warn): self
    {
        $this->maree_warn = $maree_warn;

        return $this;
    }

    public function getMareeKO(): ?string
    {
        return $this->maree_KO;
    }

    public function setMareeKO(?string $maree_KO): self
    {
        $this->maree_KO = $maree_KO;

        return $this;
    }

    public function getHauteurMBGrandeMaree()
    {
        return $this->hauteurMBGrandeMaree;
    }

    public function setHauteurMBGrandeMaree($hauteurMBGrandeMaree): self
    {
        $this->hauteurMBGrandeMaree = $hauteurMBGrandeMaree;

        return $this;
    }

    public function getHauteurMHGrandeMaree()
    {
        return $this->hauteurMHGrandeMaree;
    }

    public function setHauteurMHGrandeMaree($hauteurMHGrandeMaree): self
    {
        $this->hauteurMHGrandeMaree = $hauteurMHGrandeMaree;

        return $this;
    }

    public function getHauteurMBMoyenneMaree()
    {
        return $this->hauteurMBMoyenneMaree;
    }

    public function setHauteurMBMoyenneMaree($hauteurMBMoyenneMaree): self
    {
        $this->hauteurMBMoyenneMaree = $hauteurMBMoyenneMaree;

        return $this;
    }

    public function getHauteurMHMoyenneMaree()
    {
        return $this->hauteurMHMoyenneMaree;
    }

    public function setHauteurMHMoyenneMaree($hauteurMHMoyenneMaree): self
    {
        $this->hauteurMHMoyenneMaree = $hauteurMHMoyenneMaree;

        return $this;
    }

    public function getHauteurMBPetiteMaree()
    {
        return $this->hauteurMBPetiteMaree;
    }

    public function setHauteurMBPetiteMaree($hauteurMBPetiteMaree): self
    {
        $this->hauteurMBPetiteMaree = $hauteurMBPetiteMaree;

        return $this;
    }

    public function getHauteurMHPetiteMaree()
    {
        return $this->hauteurMHPetiteMaree;
    }

    public function setHauteurMHPetiteMaree($hauteurMHPetiteMaree): self
    {
        $this->hauteurMHPetiteMaree = $hauteurMHPetiteMaree;

        return $this;
    }

    public function getUrlBalise(): ?string
    {
        return $this->urlBalise;
    }

    public function setUrlBalise(?string $urlBalise): self
    {
        $this->urlBalise = $urlBalise;

        return $this;
    }

    public function getUrlWebcam(): ?string
    {
        return $this->urlWebcam;
    }

    public function setUrlWebcam(?string $urlWebcam): self
    {
        $this->urlWebcam = $urlWebcam;

        return $this;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion(Region $region = null): self
    {
        $this->region = $region;

        return $this;
    }


}
