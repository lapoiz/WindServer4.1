<?php
namespace App\Utils;

use App\Entity\Spot;
use Symfony\Component\Filesystem\Filesystem;


define('HauteurImg',200);
define('LargeurImg',200);

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

    /**
     * @param Spot $spot spot contenant les orientation de vent
     * Créer une image png de la rosace des vents, avec GD.
     * L'image sera stockée dans :  images/windRosaces/spotId.png
     */
    static function createRosaceWind(?Spot $spot, $urlImage) {
        try {
            $rosaceImg=RosaceWindManage::createImageRosaceWind($spot);

            $ds = DIRECTORY_SEPARATOR;

            /*$urlImage=$controller->get("kernel")->getRootDir().$ds.'..'.$ds.'web'.$ds.
                'images'.$ds.'windRosaces';

            $urlImage=$controller->getParameter('svg_directory');
            */
            RosaceWindManage::createRoute($urlImage);

            $urlImageMin=$urlImage.$ds.$spot->getId().".min.png";
            $urlImage=$urlImage.$ds.$spot->getId().".png";

            $rosaceImgMin=imagecreate(LargeurImg*ScalledImgMin,HauteurImg*ScalledImgMin);
            $blanc = imagecolorallocate($rosaceImgMin, 255, 255, 255);
            imagecopyresampled($rosaceImgMin, $rosaceImg, 0, 0, 0, 0, LargeurImg*ScalledImgMin, HauteurImg*ScalledImgMin, LargeurImg, HauteurImg);
            imagecolortransparent($rosaceImgMin, $blanc);

            imagepng($rosaceImg, $urlImage); // on enregistre l'image dans le dossier "images/windRosaces"
            imagepng($rosaceImgMin, $urlImageMin); // on enregistre l'image dans le dossier "images/windRosaces"

            imagedestroy($rosaceImg); // libération de l'espace mémoire utilisé
            imagedestroy($rosaceImgMin);

        } catch (\Exception $e) {
            $toto=$e->getMessage(); // pour debug
        }
    }

    static function getHauteurImg() {
        return HauteurImg;
    }

    static function createImageRosaceWind($spot) {
        $rosaceImg = imagecreate(LargeurImg,HauteurImg);

        $blanc = imagecolorallocate($rosaceImg, 255, 255, 255);

        RosaceWindManage::createImageFleche($rosaceImg, $spot);

        imagecolortransparent($rosaceImg, $blanc);
        return $rosaceImg;
    }

    static function createImageFleche($windRosaceImg, $spot) {

        $cercleColor = imagecolorallocate($windRosaceImg, 103, 113, 121);

        // Cercle
        imageellipse($windRosaceImg,LargeurImg/2,HauteurImg/2,2*RayonCercle,2*RayonCercle,$cercleColor); //on créé un cercle

        $listOrientation=$spot->getWindOrientation();
        $moveArray=RosaceWindManage::getMoveArray();
        $colorArray=RosaceWindManage::getColorArray($windRosaceImg);

        foreach ($listOrientation as $orientation) {
            try {
                $points = $moveArray[$orientation->getOrientation()]['points'];
                $angle =$orientation->getOrientationDeg();
                $couleur=$colorArray[$orientation->getState()];
                imagefilledpolygon($windRosaceImg, RosaceWindManage::translate_poly($points, $angle, LargeurImg / 2, HauteurImg / 2, 0, 0), 8, $couleur);
            } catch (\Exception $e) {

            }
        }
    }

    static function createPointsFleche() {
        $points = array();
        $points[]=RosaceWindManage::getXGD(LargeurImg/2);
        $points[]=RosaceWindManage::getYGD((HauteurImg/2)+RayonCentral);// on commence par la pointe de la fleche

        // on tourne dans le sens des aiguilles
        $points[]=RosaceWindManage::getXGD(LargeurImg/2-LargeurPointeFlechePrincipal/2); // X pointe gauche
        $points[]=RosaceWindManage::getYGD(HauteurImg/2+RayonCentral+HauteurPointeFlechePrincipal);// Y pointe gauche

        $points[]=RosaceWindManage::getXGD(LargeurImg/2-LargeurPointeFlechePrincipal/10); // X creux gauche de la pointe
        $points[]=RosaceWindManage::getYGD(HauteurImg/2+RayonCentral+HauteurPointeFlechePrincipal*2/3); // Y creux gauche de la pointe

        $points[]=RosaceWindManage::getXGD(LargeurImg/2-LargeurTroncFlechePrincipal/2); // X Base gauche
        $points[]=RosaceWindManage::getYGD(HauteurImg); // Y Base gauche

        $points[]=RosaceWindManage::getXGD(LargeurImg/2); // X Creux du tronc
        $points[]=RosaceWindManage::getYGD(HauteurImg-HauteurTroncFlechePrincipal); // Y Creux du tronc

        $points[]=RosaceWindManage::getXGD(LargeurImg/2+LargeurTroncFlechePrincipal/2); // X Base droite
        $points[]=RosaceWindManage::getYGD(HauteurImg); // Y Base droite

        $points[]=RosaceWindManage::getXGD(LargeurImg/2+LargeurPointeFlechePrincipal/10); // X creux droite de la pointe
        $points[]=RosaceWindManage::getYGD(HauteurImg/2+RayonCentral+HauteurPointeFlechePrincipal*2/3); // Y creux doite de la pointe

        $points[]=RosaceWindManage::getXGD(LargeurImg/2+LargeurPointeFlechePrincipal/2); // X pointe droite
        $points[]=RosaceWindManage::getYGD(HauteurImg/2+RayonCentral+HauteurPointeFlechePrincipal);// Y pointe droite

        return $points;
    }

    static function reduceArrow($point_array, $scale, $fixedX, $fixedY) {
        $reduce_poly = Array();
        while(count($point_array) > 1)
        {
            $temp_x = RosaceWindManage::getX(array_shift($point_array));
            $temp_y = RosaceWindManage::getY(array_shift($point_array));
            RosaceWindManage::reduce_point($temp_x, $temp_y, $scale, $fixedX, $fixedY);
            array_push($reduce_poly, $temp_x);
            array_push($reduce_poly, $temp_y);
        }
        return $reduce_poly;
    }

    static function reduce_point(&$x,&$y,$scale, $fixedX, $fixedY)
    {
        $x = RosaceWindManage::getXGD(RosaceWindManage::reduce_ordonnee($x,$scale,$fixedX));
        $y = RosaceWindManage::getYGD(RosaceWindManage::reduce_ordonnee($y,$scale,$fixedY));
    }

    /**
     * @param $ord
     * @param $scale
     * @param $fixedOrd
     * @return Dans le systeme de coordonnées GD
     * Dans le system de coordonnée normal
     */
    static function reduce_ordonnee($ord, $scale, $fixedOrd)
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

    static function translate_point(&$x,&$y,$angle,$about_x,$about_y,$shift_x,$shift_y)
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

    static function translate_poly($point_array, $angle, $about_x, $about_y,$shift_x,$shift_y)
    {
        $translated_poly = Array();
        while(count($point_array) > 1)
        {
            $temp_x = array_shift($point_array);
            $temp_y = array_shift($point_array);
            RosaceWindManage::translate_point($temp_x, $temp_y, $angle, $about_x, $about_y,$shift_x, $shift_y);
            array_push($translated_poly, $temp_x);
            array_push($translated_poly, $temp_y);
        }
        return $translated_poly;
    }




    /**
     * @param x valeur de x dans le system classique (en bas à gauche)
     * @return la valeur de x, dans un system de coordonnée de GD (en haut à gauche)
     */
    static function getXGD($x) {
        return $x;
    }

    /**
     * @param $yGD valeur de y dans le system de coordonnée classique (en bas à gauche)
     * @return la valeur de y, dans un system de GD (en haut à gauche)
     */
    static function getYGD($y) {
        return HauteurImg-$y;
    }

    /**
     * @param xGD valeur de x dans le system de coordonnée de GD (en haut à gauche)
     * @return la valeur de x, dans un system classique (en bas à gauche)
     */
    static function getX($xGD) {
        return $xGD;
    }

    /**
     * @param $yGD valeur de y dans le system de GD (en haut à gauche)
     * @return la valeur de y, dans un system de coordonnée classique (en bas à gauche)
     */
    static function getY($yGD) {
        return HauteurImg-$yGD;
    }

    /**
     * crée un dossier si il n'existe pas
     */
    static function createRoute($route)
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

    static function getMoveArray() {
        $result=array();

        $points = RosaceWindManage::createPointsFleche();
        $pointsHalf=RosaceWindManage::reduceArrow($points,ScalledHalf,LargeurImg/2,HauteurImg/2+RayonCentral);
        $pointsQuarter=RosaceWindManage::reduceArrow($points,ScalledQuater,LargeurImg/2,HauteurImg/2+RayonCentral);

        $result['n']=RosaceWindManage::getMoveElemArray(0,$points);
        $result['nnw']=RosaceWindManage::getMoveElemArray(22.5,$pointsQuarter);
        $result['nw']=RosaceWindManage::getMoveElemArray(45,$pointsHalf);
        $result['wnw']=RosaceWindManage::getMoveElemArray(67.5,$pointsQuarter);

        $result['w']=RosaceWindManage::getMoveElemArray(90,$points);
        $result['wsw']=RosaceWindManage::getMoveElemArray(112.5,$pointsQuarter);
        $result['sw']=RosaceWindManage::getMoveElemArray(135,$pointsHalf);
        $result['ssw']=RosaceWindManage::getMoveElemArray(157.5,$pointsQuarter);

        $result['s']=RosaceWindManage::getMoveElemArray(180,$points);
        $result['sse']=RosaceWindManage::getMoveElemArray(202.5,$pointsQuarter);
        $result['se']=RosaceWindManage::getMoveElemArray(225,$pointsHalf);
        $result['ese']=RosaceWindManage::getMoveElemArray(247.5,$pointsQuarter);

        $result['e']=RosaceWindManage::getMoveElemArray(270,$points);
        $result['ene']=RosaceWindManage::getMoveElemArray(292.5,$pointsQuarter);
        $result['ne']=RosaceWindManage::getMoveElemArray(315,$pointsHalf);
        $result['nne']=RosaceWindManage::getMoveElemArray(337.5,$pointsQuarter);

        return $result;
    }

    static function getMoveElemArray($orient, $points) {
         return array('orient'=>$orient, 'points'=>$points);
    }

    static function getColorArray($windRosaceImg)
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