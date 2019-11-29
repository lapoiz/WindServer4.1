<?php

namespace App\Service;


use App\Entity\Spot;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;


define('HauteurImgMaree',400);
define('LargeurImgMaree',400);

define('MargeXImgMaree',40);
define('MargeYImgMaree',40);

define('NbPointSin',200);
define('FontSize',4); // de 1 à 5
define('HauteurMareeMax',12);


class MareeToImage
{
    private $container;
    private $white, $blue, $green, $red, $orange;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param Spot $spot
     */
    public function createImageMareeFromSpot(Spot $spot) {
        // Liste de contrainte, avec contrainte = ['hMin'=> 0, 'hMax'=> 6, 'state'=>'OK']
        $contraintes = [];

        foreach ($spot->getMareeRestriction() as $restriction ) {
            $contraintes[]=['hMin'=>$restriction->getHauteurMin(),'hMax'=>$restriction->getHauteurMax(),'state'=>$restriction->getState()];
        }

        $this->createImageMaree($spot,$spot->getHauteurMHGrandeMaree(), $spot->getHauteurMBGrandeMaree(), $spot->getHauteurMHPetiteMaree(),
            $spot->getHauteurMBPetiteMaree(),$spot->getHauteurMHMoyenneMaree(), $spot->getHauteurMBMoyenneMaree(),
            $contraintes);
    }

    public function createImageMaree(Spot $spot, $hauteurMHGrandeMaree, $hauteurMBGrandeMaree, $hauteurMHPetiteMaree,
                                     $hauteurMBPetiteMaree,$hauteurMHMoyenneMaree, $hauteurMBMoyenneMaree,
                                    $contraintes ) {

        try {
            $immagePath = $this->container->getParameter('maree_directory_kernel').DIRECTORY_SEPARATOR.'maree.'.$spot->getId().'.jpg';

            $filesystem = new Filesystem();

            if ($filesystem->exists($immagePath)) {
                $filesystem->remove($immagePath);
            }

            // Création d'une image
            $image=imagecreate(LargeurImgMaree,HauteurImgMaree);

            // Alloue quelques couleurs
            $blanc = imagecolorallocate($image, 255, 255, 255); // 1er -> couleur de fond de l'image

            global $white, $blue, $green, $red, $orange;
            $blue = imagecolorallocate($image, 52, 152, 219);
            $black = imagecolorallocate($image, 0, 0, 0);
            $green = imagecolorallocate($image, 30, 132, 73);
            $red = imagecolorallocate($image, 231, 76, 60);
            $orange = imagecolorallocate($image, 255, 165, 0);

            $this->dessineContraintes($image, $contraintes);

            // Dessine les axes
            $this->dessineAxes($image, $black);

            // Définition du tablau de points
            $tabPointGrandeMaree=$this->generatePointSinu($hauteurMHGrandeMaree, $hauteurMBGrandeMaree);
            $tabPointPetiteMaree=$this->generatePointSinu($hauteurMHPetiteMaree, $hauteurMBPetiteMaree);
            $tabPointMoyenneMaree=$this->generatePointSinu($hauteurMHMoyenneMaree, $hauteurMBMoyenneMaree);

            // Dessine les courbes
            $this->dessineCourbe($image, $tabPointGrandeMaree, $black,true);
            $this->dessineCourbe($image, $tabPointPetiteMaree, $black,true);
            $this->dessineCourbe($image, $tabPointMoyenneMaree, $black,false);

            imagepng($image, $immagePath); // on enregistre l'image

            imagedestroy($image); // libération de l'espace mémoire utilisé
        } catch (\Exception $e) {
            $toto=$e->getMessage(); // pour debug
        }
    }

    /**
     * Généré les points de la sinusoïdale en fonction de la hauteur Maree Basse, de la hauteur Maree Haute et de nombre de points (NbPointSin)
     */
    private function generatePointSinu($hauteurMH, $hauteurMB)
    {
        $tabPoints = array();
        $coefI=(LargeurImgMaree-MargeXImgMaree)/NbPointSin;
        $coefX=3*pi()/(LargeurImgMaree-MargeXImgMaree);
        $y0=($hauteurMB+$hauteurMH)/2;
        $amplitudeY=($hauteurMB+$hauteurMH)/2-$hauteurMB;
        $coefY=(HauteurImgMaree-MargeYImgMaree)/HauteurMareeMax;

        for ($i = 0; $i < NbPointSin; $i++) {
            $x=$i*$coefI;
            $tabPoints[]=$this->getXGD($x+MargeXImgMaree);
            $tabPoints[]=$this->getYGD(($amplitudeY*sin($x*$coefX)+$y0)*$coefY+MargeYImgMaree);
            //$tabPoints[]=$this->getYGD(sin($x));
        }
        return $tabPoints;
    }

    private function dessineCourbe($image, $tabPoint, $color, $isTiree) {
        $nbPoint=count($tabPoint);
        $nbPair=$nbPoint/2;
        $iSWhite=true;

        for ($i = 1; $i < $nbPair; $i++) {
            if ($isTiree) {
                if ($iSWhite) {
                    $iSWhite = false;
                } else {
                    ImageLine($image, $tabPoint[$i * 2 - 2], $tabPoint[$i * 2 - 1], $tabPoint[$i * 2], $tabPoint[$i * 2 + 1], $color);
                    $iSWhite = true;
                }
            } else
                ImageLine($image, $tabPoint[$i * 2 - 2], $tabPoint[$i * 2 - 1], $tabPoint[$i * 2], $tabPoint[$i * 2 + 1], $color);
        }
    }

    private function dessineContraintes($image,$contraintes) {
        if (null!=$contraintes) {
            foreach ($contraintes as &$contrainte) {
                // contrainte = ['hMin'=> 0, 'hMax'=> 6, 'state'=>'OK']

                imagefilledrectangle ($image,
                    $this->getXGDNew(0), $this->getYGDforH($contrainte['hMin']),
                    $this->getXGDNewMax(), $this->getYGDforH($contrainte['hMax']),
                    $this->getColorState($contrainte['state']));
            }
        }
    }

    private function dessineAxes($image,$color) {
        $hauteurFleche=10;
        // Axe des X
        ImageLine($image,$this->getXGD(MargeXImgMaree), $this->getYGD(MargeYImgMaree), $this->getXGD(LargeurImgMaree-$hauteurFleche),$this->getYGD(MargeYImgMaree), $color);
        $pointFlecheX=array($this->getXGD(LargeurImgMaree-$hauteurFleche),$this->getYGD(MargeYImgMaree),
            $this->getXGD(LargeurImgMaree-$hauteurFleche-$hauteurFleche/3),$this->getYGD(MargeYImgMaree+$hauteurFleche/3),
            $this->getXGD(LargeurImgMaree),$this->getYGD(MargeYImgMaree),
            $this->getXGD(LargeurImgMaree-$hauteurFleche-$hauteurFleche/3),$this->getYGD(MargeYImgMaree-$hauteurFleche/3)
        );
        ImagePolygon($image,$pointFlecheX,4,$color);

        // Axe des Y
        ImageLine($image,$this->getXGD(MargeXImgMaree), $this->getYGD(MargeYImgMaree), $this->getXGD(MargeXImgMaree),$this->getYGD(HauteurImgMaree-$hauteurFleche), $color);
        $pointFlecheY=array(
            $this->getXGD(MargeXImgMaree),$this->getYGD(HauteurImgMaree-$hauteurFleche),
            $this->getXGD(MargeXImgMaree-$hauteurFleche/3),$this->getYGD(HauteurImgMaree-$hauteurFleche-$hauteurFleche/3),
            $this->getXGD(MargeXImgMaree),$this->getYGD(HauteurImgMaree),
            $this->getXGD(MargeXImgMaree+$hauteurFleche/3),$this->getYGD(HauteurImgMaree-$hauteurFleche-$hauteurFleche/3),
        );
        ImagePolygon($image,$pointFlecheY,4,$color);

        // tirées et valeurs de l'axe des Y
        for ($i=1;$i<HauteurMareeMax-1;$i++) {
            ImageLine($image,
                $this->getXGD(MargeXImgMaree-2), $this->getYGD(MargeYImgMaree+$i*(HauteurImgMaree-MargeYImgMaree)/HauteurMareeMax),
                $this->getXGD(MargeXImgMaree+2),$this->getYGD(MargeYImgMaree+$i*(HauteurImgMaree-MargeYImgMaree)/HauteurMareeMax),
                $color);
            ImageString($image,FontSize,$this->getXGD(MargeXImgMaree-15),$this->getYGD((MargeYImgMaree+$i*(HauteurImgMaree-MargeYImgMaree)/HauteurMareeMax)+8),$i,$color);
        }


    }










    /**
     * @param x valeur de x dans le system classique (en bas à gauche)
     * @return la valeur de x, dans un system de coordonnée de GD (en haut à gauche)
     */
    function getXGD($x) {
        return $x;
    }

    /**
     * @param $yGD valeur de y dans le system de coordonnée classique (en bas à gauche)
     * @return la valeur de y, dans un system de GD (en haut à gauche)
     */
    function getYGD($y) {
        return HauteurImgMaree-$y;
    }

    /**
     * @param xGD valeur de x dans le system de coordonnée de GD (en haut à gauche)
     * @return la valeur de x, dans un system classique (en bas à gauche)
     */
    function getX($xGD) {
        return $xGD;
    }

    /**
     * @param $yGD valeur de y dans le system de GD (en haut à gauche)
     * @return la valeur de y, dans un system de coordonnée classique (en bas à gauche)
     */
    function getY($yGD) {
        return HauteurImgMaree-$yGD;
    }


    /**
     * @param $hauteurMaree valeur de la hauteur de la marée en m.
     * @return la valeur de y, dans un system de GD (en haut à gauche)
     */
    function getYGDforH($hauteurMaree) {
        return $this->getYGD(MargeYImgMaree+$hauteurMaree*(HauteurImgMaree-MargeYImgMaree)/HauteurMareeMax);
    }


    /**
     * @param $xNew valeur de x dans le nouveau référentiel (des axes).
     * @return la valeur de x, dans un system de GD (en haut à gauche)
     */
    function getXGDNew($xNew) {
        return $this->getXGD(MargeXImgMaree+$xNew);
    }

    /**
     * @return la valeur max de x, dans un system de GD (en haut à gauche)
     */
    function getXGDNewMax() {
        return $this->getXGD(LargeurImgMaree);
    }


    function getColorState($state) {
        global $blue, $green, $red, $orange;
        switch ($state) {
            case 'top':
                return $blue;
                break;
            case 'OK':
                return $green;
                break;
            case 'warn':
                return $orange;
                break;
            case 'KO':
                return $red;
                break;
        }
    }

}