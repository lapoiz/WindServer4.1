<?php
namespace App\Service;

use App\Entity\Spot;
use Symfony\Component\Filesystem\Filesystem;


define('HauteurImgRosace',200);
define('LargeurImgRosace',200);

define('ScalledImgMin',0.25);

define('ScalledHalf',0.8);
define('ScalledQuater',0.7);

define('RayonCentral',20);
define('RayonCercle',60);

define('LargeurTroncFlechePrincipal',20);
define('HauteurTroncFlechePrincipal',10);
define('LargeurPointeFlechePrincipal',16);
define('HauteurPointeFlechePrincipal',12);


class RosaceWindManage
{
    /*
    public function __construct(ContainerInterface $container)
    {

    }
    */

    /**
     * @param Spot $spot spot contenant les orientation de vent
     * Créer une image png de la rosace des vents, avec GD.
     * L'image sera stockée dans :  images/windRosaces/spotId.png
     */
    public function createRosaceWind(?Spot $spot, $urlImage) {
        try {
            $rosaceImg= $this->createImageRosaceWind($spot);

            $ds = DIRECTORY_SEPARATOR;

            /*$urlImage=$controller->get("kernel")->getRootDir().$ds.'..'.$ds.'web'.$ds.
                'images'.$ds.'windRosaces';

            $urlImage=$controller->getParameter('svg_directory');
            */
            $this->createRoute($urlImage);

            $urlImageMin=$urlImage.$ds.$spot->getId().".min.png";
            $urlImage=$urlImage.$ds.$spot->getId().".png";

            $rosaceImgMin=imagecreate(LargeurImgRosace*ScalledImgMin,HauteurImgRosace*ScalledImgMin);
            $blanc = imagecolorallocate($rosaceImgMin, 255, 255, 255);
            imagecopyresampled($rosaceImgMin, $rosaceImg, 0, 0, 0, 0, LargeurImgRosace*ScalledImgMin, HauteurImgRosace*ScalledImgMin, LargeurImgRosace, HauteurImgRosace);
            imagecolortransparent($rosaceImgMin, $blanc);

            imagepng($rosaceImg, $urlImage); // on enregistre l'image dans le dossier "images/windRosaces"
            imagepng($rosaceImgMin, $urlImageMin); // on enregistre l'image dans le dossier "images/windRosaces"

            imagedestroy($rosaceImg); // libération de l'espace mémoire utilisé
            imagedestroy($rosaceImgMin);

        } catch (\Exception $e) {
            $toto=$e->getMessage(); // pour debug
        }
    }

    private function getHauteurImgRosace() {
        return HauteurImgRosace;
    }

    public function createImageRosaceWind($spot) {
        $rosaceImg = imagecreate(LargeurImgRosace,HauteurImgRosace);

        $blanc = imagecolorallocate($rosaceImg, 255, 255, 255);

        $this->createImageFleche($rosaceImg, $spot);

        imagecolortransparent($rosaceImg, $blanc);
        return $rosaceImg;
    }

    private function createImageFleche($windRosaceImg, $spot) {

        $cercleColor = imagecolorallocate($windRosaceImg, 103, 113, 121);

        // Cercle
        imageellipse($windRosaceImg,LargeurImgRosace/2,HauteurImgRosace/2,2*RayonCercle,2*RayonCercle,$cercleColor); //on créé un cercle

        $listOrientation=$spot->getWindOrientation();
        $moveArray=$this->getMoveArray();
        $colorArray=$this->getColorArray($windRosaceImg);

        foreach ($listOrientation as $orientation) {
            try {
                $points = $moveArray[$orientation->getOrientation()]['points'];
                $angle =$orientation->getOrientationDeg();
                $couleur=$colorArray[$orientation->getState()];
                imagefilledpolygon($windRosaceImg, $this->translate_poly($points, $angle, LargeurImgRosace / 2, HauteurImgRosace / 2, 0, 0), 8, $couleur);
            } catch (\Exception $e) {

            }
        }
    }

    private function createPointsFleche() {
        $points = array();
        $points[]=$this->getXGD(LargeurImgRosace/2);
        $points[]=$this->getYGD((HauteurImgRosace/2)+RayonCentral);// on commence par la pointe de la fleche

        // on tourne dans le sens des aiguilles
        $points[]=$this->getXGD(LargeurImgRosace/2-LargeurPointeFlechePrincipal/2); // X pointe gauche
        $points[]=$this->getYGD(HauteurImgRosace/2+RayonCentral+HauteurPointeFlechePrincipal);// Y pointe gauche

        $points[]=$this->getXGD(LargeurImgRosace/2-LargeurPointeFlechePrincipal/10); // X creux gauche de la pointe
        $points[]=$this->getYGD(HauteurImgRosace/2+RayonCentral+HauteurPointeFlechePrincipal*2/3); // Y creux gauche de la pointe

        $points[]=$this->getXGD(LargeurImgRosace/2-LargeurTroncFlechePrincipal/2); // X Base gauche
        $points[]=$this->getYGD(HauteurImgRosace); // Y Base gauche

        $points[]=$this->getXGD(LargeurImgRosace/2); // X Creux du tronc
        $points[]=$this->getYGD(HauteurImgRosace-HauteurTroncFlechePrincipal); // Y Creux du tronc

        $points[]=$this->getXGD(LargeurImgRosace/2+LargeurTroncFlechePrincipal/2); // X Base droite
        $points[]=$this->getYGD(HauteurImgRosace); // Y Base droite

        $points[]=$this->getXGD(LargeurImgRosace/2+LargeurPointeFlechePrincipal/10); // X creux droite de la pointe
        $points[]=$this->getYGD(HauteurImgRosace/2+RayonCentral+HauteurPointeFlechePrincipal*2/3); // Y creux doite de la pointe

        $points[]=$this->getXGD(LargeurImgRosace/2+LargeurPointeFlechePrincipal/2); // X pointe droite
        $points[]=$this->getYGD(HauteurImgRosace/2+RayonCentral+HauteurPointeFlechePrincipal);// Y pointe droite

        return $points;
    }

    private function reduceArrow($point_array, $scale, $fixedX, $fixedY) {
        $reduce_poly = Array();
        while(count($point_array) > 1)
        {
            $temp_x = $this->getX(array_shift($point_array));
            $temp_y = $this->getY(array_shift($point_array));
            $this->reduce_point($temp_x, $temp_y, $scale, $fixedX, $fixedY);
            array_push($reduce_poly, $temp_x);
            array_push($reduce_poly, $temp_y);
        }
        return $reduce_poly;
    }

    private function reduce_point(&$x,&$y,$scale, $fixedX, $fixedY)
    {
        $x = $this->getXGD($this->reduce_ordonnee($x,$scale,$fixedX));
        $y = $this->getYGD($this->reduce_ordonnee($y,$scale,$fixedY));
    }

    /**
     * @param $ord
     * @param $scale
     * @param $fixedOrd
     * @return Dans le systeme de coordonnées GD
     * Dans le system de coordonnée normal
     */
    private function reduce_ordonnee($ord, $scale, $fixedOrd)
    {
        if ($ord>$fixedOrd) {
            return $ord-($ord-$fixedOrd)*(1-$scale);
        } elseif ($ord<$fixedOrd) {
            return $ord+($fixedOrd-$ord)*(1-$scale);
        } else {
            // ==
            return $ord;
        }
    }

    private function translate_point(&$x,&$y,$angle,$about_x,$about_y,$shift_x,$shift_y)
    {
        $x -= $about_x;
        $y -= $about_y;
        $angle = ($angle / 180) * M_PI;
        /* math:
        [x2,y2] = [x,  *  [[cos(a),-sin(a)],
                   y]      [sin(a),cos(a)]]
        ==>
        x = x * cos(a) + y*sin(a)
        y = x*-sin(a) + y*cos(a)
        */

        $new_x = $x * cos($angle) - $y * sin($angle);
        $new_y = $x * sin($angle) + $y * cos($angle);
        $x = $new_x+ $about_x + $shift_x ;
        $y = $new_y + $about_y + $shift_y;
    }

    private function translate_poly($point_array, $angle, $about_x, $about_y,$shift_x,$shift_y)
    {
        $translated_poly = Array();
        while(count($point_array) > 1)
        {
            $temp_x = array_shift($point_array);
            $temp_y = array_shift($point_array);
            $this->translate_point($temp_x, $temp_y, $angle, $about_x, $about_y,$shift_x, $shift_y);
            array_push($translated_poly, $temp_x);
            array_push($translated_poly, $temp_y);
        }
        return $translated_poly;
    }




    /**
     * @param x valeur de x dans le system classique (en bas à gauche)
     * @return la valeur de x, dans un system de coordonnée de GD (en haut à gauche)
     */
    private function getXGD($x) {
        return $x;
    }

    /**
     * @param $yGD valeur de y dans le system de coordonnée classique (en bas à gauche)
     * @return la valeur de y, dans un system de GD (en haut à gauche)
     */
    private function getYGD($y) {
        return HauteurImgRosace-$y;
    }

    /**
     * @param xGD valeur de x dans le system de coordonnée de GD (en haut à gauche)
     * @return la valeur de x, dans un system classique (en bas à gauche)
     */
    private function getX($xGD) {
        return $xGD;
    }

    /**
     * @param $yGD valeur de y dans le system de GD (en haut à gauche)
     * @return la valeur de y, dans un system de coordonnée classique (en bas à gauche)
     */
    private function getY($yGD) {
        return HauteurImgRosace-$yGD;
    }

    /**
     * crée un dossier si il n'existe pas
     */
    private function createRoute($route)
    {
        $fs = new Filesystem();
        if( !is_dir($route) )
        {
            try {
                $fs->mkdir($route, 0755);
            }
            catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
            }
        }
    }

    private function getMoveArray() {
        $result=array();

        $points = $this->createPointsFleche();
        $pointsHalf=$this->reduceArrow($points,ScalledHalf,LargeurImgRosace/2,HauteurImgRosace/2+RayonCentral);
        $pointsQuarter=$this->reduceArrow($points,ScalledQuater,LargeurImgRosace/2,HauteurImgRosace/2+RayonCentral);

        $result['n']=$this->getMoveElemArray(0,$points);
        $result['nnw']=$this->getMoveElemArray(22.5,$pointsQuarter);
        $result['nw']=$this->getMoveElemArray(45,$pointsHalf);
        $result['wnw']=$this->getMoveElemArray(67.5,$pointsQuarter);

        $result['w']=$this->getMoveElemArray(90,$points);
        $result['wsw']=$this->getMoveElemArray(112.5,$pointsQuarter);
        $result['sw']=$this->getMoveElemArray(135,$pointsHalf);
        $result['ssw']=$this->getMoveElemArray(157.5,$pointsQuarter);

        $result['s']=$this->getMoveElemArray(180,$points);
        $result['sse']=$this->getMoveElemArray(202.5,$pointsQuarter);
        $result['se']=$this->getMoveElemArray(225,$pointsHalf);
        $result['ese']=$this->getMoveElemArray(247.5,$pointsQuarter);

        $result['e']=$this->getMoveElemArray(270,$points);
        $result['ene']=$this->getMoveElemArray(292.5,$pointsQuarter);
        $result['ne']=$this->getMoveElemArray(315,$pointsHalf);
        $result['nne']=$this->getMoveElemArray(337.5,$pointsQuarter);

        return $result;
    }

    private function getMoveElemArray($orient, $points) {
         return array('orient'=>$orient, 'points'=>$points);
    }

    private function getColorArray($windRosaceImg)
    {
        $result = array();
        $result['warn'] = imagecolorallocate($windRosaceImg, 255, 127, 0);
        $result['KO'] = imagecolorallocate($windRosaceImg, 187, 11, 11);
        $result['OK'] = imagecolorallocate($windRosaceImg, 0, 64, 0);
        $result['top'] = imagecolorallocate($windRosaceImg, 0, 0, 80);
        $result['?'] = imagecolorallocate($windRosaceImg, 206, 206, 206);

        return $result;
    }
}