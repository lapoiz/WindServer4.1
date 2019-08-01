<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SpotRepository")
 * @Vich\Uploadable
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
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $price_autoroute_from_paris;

    /**
     * @ORM\Column(type="decimal", scale=6, nullable=true)
     */
    private $gps_lat;

    /**
     * @ORM\Column(type="decimal", scale=6, nullable=true)
     */
    private $gps_long;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_orientation_vent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $URLMap;

    /**
     * @ORM\Column(type="string", length=512, nullable=true)
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
     * @ORM\OneToMany(targetEntity="MareeRestriction", mappedBy="spot", cascade={"remove", "persist"} , orphanRemoval=true)
     */
    private $mareeRestriction;

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
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="spots", cascade={"persist"})
     */
    private $region;

    /**
     * @ORM\OneToMany(targetEntity="WindOrientation", mappedBy="spot", cascade={"remove", "persist"} , orphanRemoval=true)
     */
    private $windOrientation;


    /**
     * @ORM\OneToMany(targetEntity="WebSiteInfo", mappedBy="spot", cascade={"remove", "persist"} , orphanRemoval=true)
     */
    private $webSiteInfos;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codeSpot;



    /**
     * @Vich\UploadableField(mapping="spot_image", fileNameProperty="imageName")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFoil;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_foil;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isContraintEte;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_contraintEte;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $desc_wave;

    /**
     * @ORM\OneToMany(targetEntity="Commentaire", mappedBy="spot", cascade={"remove", "persist"} , orphanRemoval=true)
     */
    private $commentaires;

    public function __construct()
    {
        $this->mareeRestriction = new ArrayCollection();
        $this->windOrientation = new ArrayCollection();
        $this->webSiteInfos = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->buildWindOrientation();
    }

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

    public function getPriceAutorouteFromParis()
    {
        return $this->price_autoroute_from_paris;
    }

    public function setPriceAutorouteFromParis( $price_autoroute_from_paris): self
    {
        $this->price_autoroute_from_paris = $price_autoroute_from_paris;

        return $this;
    }

    public function getGpsLat()
    {
        return $this->gps_lat;
    }

    public function setGpsLat($gps_lat): self
    {
        $this->gps_lat = $gps_lat;

        return $this;
    }

    public function getGpsLong()
    {
        return $this->gps_long;
    }

    public function setGpsLong($gps_long): self
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

    /**
     * @return mixed
     */
    public function getURLMap()
    {
        return $this->URLMap;
    }

    /**
     * @param mixed $URLMap
     * @return Spot
     */
    public function setURLMap($URLMap)
    {
        $this->URLMap = $URLMap;
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

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region = null): self
    {
        $this->region = $region;

        return $this;
    }

    public function addMareeRestriction(MareeRestriction $mareeRestriction)
    {
        $this->mareeRestriction[] = $mareeRestriction;

        return $this;
    }


    public function removeMareeRestriction(MareeRestriction $mareeRestriction)
    {
        $this->mareeRestriction->removeElement($mareeRestriction);
    }

    public function getMareeRestriction()
    {
        return $this->mareeRestriction;
    }

    public function addWindOrientation(?WindOrientation $windOrientation): self
    {
        $this->windOrientation[] = $windOrientation;
        return $this;
    }

    public function removeWindOrientation(?WindOrientation $windOrientation)
    {
        $this->windOrientation->removeElement($windOrientation);
    }

    public function getWindOrientation() 
    {
        return $this->windOrientation;
    }



    private function buildWindOrientation()
    {
        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("n");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("nne");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("ne");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("ene");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("e");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("ese");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("se");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("sse");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("s");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("ssw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("sw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("wsw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("w");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("wnw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("nw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);

        $windOrientation = new WindOrientation();
        $windOrientation->setSpot($this);
        $windOrientation->setOrientation("nnw");
        //$windOrientation->setOrientationDeg(WebsiteGetData::transformeOrientationNomLongDeg($windOrientation->getOrientation()));
        $this->windOrientation->add($windOrientation);
    }

    public function getWebSiteInfos()
    {
        return $this->webSiteInfos;
    }

    /**public function setWebSiteInfos($webSiteInfos)
    {
        $this->webSiteInfos = $webSiteInfos;
        return $this;
    }*/

    public function addWebSiteInfos(WebSiteInfo $webSiteInfo)
    {
        $this->webSiteInfos[] = $webSiteInfo;
        return $this;
    }

    public function removeWebSiteInfo(WebSiteInfo $webSiteInfo)
    {
        $this->webSiteInfos->removeElement($webSiteInfo);
    }


    public function getCodeSpot(): ?string
    {
        return $this->codeSpot;
    }

    public function setCodeSpot(string $codeSpot): self
    {
        $this->codeSpot = $codeSpot;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile): void
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }
    }

    /**
     * @return string|null
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * @param string|null $imageName
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPersonalNamer(): ?string
    {
        return $this->getCodeSpot();
    }

    public function getIsFoil(): ?bool
    {
        return $this->isFoil;
    }

    public function setIsFoil(?bool $isFoil): self
    {
        $this->isFoil = $isFoil;

        return $this;
    }

    public function getDescFoil(): ?string
    {
        return $this->desc_foil;
    }

    public function setDescFoil(?string $desc_foil): self
    {
        $this->desc_foil = $desc_foil;

        return $this;
    }

    public function getIsContraintEte(): ?bool
    {
        return $this->isContraintEte;
    }

    public function setIsContraintEte(?bool $isContraintEte): self
    {
        $this->isContraintEte = $isContraintEte;

        return $this;
    }

    public function getDescContraintEte(): ?string
    {
        return $this->desc_contraintEte;
    }

    public function setDescContraintEte(?string $desc_contraintEte): self
    {
        $this->desc_contraintEte = $desc_contraintEte;

        return $this;
    }

    public function getDescWave(): ?string
    {
        return $this->desc_wave;
    }

    public function setDescWave(?string $desc_wave): self
    {
        $this->desc_wave = $desc_wave;

        return $this;
    }

    public function getCommentaires()
    {
        return $this->commentaires;
    }

    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
        return $this;
    }

    public function addCommentaire(Commentaire $newComment) {
        $this->commentaires->add($newComment);
    }
}
