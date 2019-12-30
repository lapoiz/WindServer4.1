<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 13/09/2019
 * Time: 20:35
 */

namespace App\Service;


use App\Entity\Spot;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class HTMLtoImage
{
    private $cardImageDirectoryKernel;
    private $templating;
    private $container;

    public function __construct(\Twig_Environment $templating, ContainerInterface $container, $cardImageDirectoryKernel)
    {
        $this->templating = $templating;
        $this->container = $container;
        $this->cardImageDirectoryKernel = $cardImageDirectoryKernel;
    }

    public function createImageCard(Spot $spot) {

        $emptyImagePath = $this->cardImageDirectoryKernel.DIRECTORY_SEPARATOR.'emptyCard.'.$spot->getId().'.jpg';
        $imagePath = $this->cardImageDirectoryKernel.DIRECTORY_SEPARATOR.'card.'.$spot->getId().'.jpg';
        $urlRosaceImage = $this->container->getParameter('rosace_directory_kernel').DIRECTORY_SEPARATOR.$spot->getId().'.png';
        $urlMareeImage = $this->container->getParameter('maree_directory_kernel').DIRECTORY_SEPARATOR.'maree.'.$spot->getId().'.jpg';
        //$utlFoilImage = $this->container->getParameter('image_directory_kernel').DIRECTORY_SEPARATOR.'foil.png';
        //$utlInterditImage = $this->container->getParameter('image_directory_kernel').DIRECTORY_SEPARATOR.'interdit.png';
        //$utlEteImage = $this->container->getParameter('image_directory_kernel').DIRECTORY_SEPARATOR.'été.png';

        $filesystem = new Filesystem();
        $this->removeFile($filesystem,$emptyImagePath);
        $this->removeFile($filesystem,$imagePath);

        $snappyImage = $this->container->get('knp_snappy.image');
        //$snappyImage->setOption('quality', 100); // image trop lourd pour peu d'avantage
        $snappyImage->setOption('javascript-delay', 1000);
        $snappyImage->setOption('no-stop-slow-scripts', true);

        $snappyImage->generateFromHtml(
            $this->templating->render(
                'spot/emptyCard.html.twig',
                array(
                    'spot'  => $spot,
                )
            ),
            $emptyImagePath
        );
        $emptyImage = imagecreatefromjpeg($emptyImagePath);
        $rosaceImage= imagecreatefrompng($urlRosaceImage);
        //$foilImage= imagecreatefrompng($utlFoilImage);
        //$eteImage= imagecreatefrompng($utlEteImage);
        //$interditImage= imagecreatefrompng($utlInterditImage);

        imagecopyresampled($emptyImage, $rosaceImage,5,5,0,0,120, 120,imagesx($rosaceImage), imagesy($rosaceImage));

        if ($spot->getURLMaree() != null && !empty($spot->getURLMaree()) && file_exists($urlMareeImage)) {
            $mareeImage = imagecreatefromjpeg($urlMareeImage);
            imagecopyresampled($emptyImage,$mareeImage,5,365,0,0,120, 120,imagesx($mareeImage),imagesy($mareeImage));
        }

        /*
        imagecopyresampled($emptyImage, $foilImage,180,95,0,0,30, 30,imagesx($foilImage), imagesy($foilImage));
        imagecopyresampled($emptyImage, $eteImage,220,95,0,0,30, 30,imagesx($eteImage), imagesy($eteImage));

        if (!$spot->getIsFoil()) {
            imagecopyresampled($emptyImage, $interditImage,180,95,0,0,30, 30,imagesx($interditImage), imagesy($interditImage));
        }
        if (!$spot->getIsContraintEte()) {
            imagecopyresampled($emptyImage, $interditImage,220,95,0,0,30, 30,imagesx($interditImage), imagesy($interditImage));
        }
        */

        imagejpeg($emptyImage,$imagePath);

        /* with card.html.twig
        $snappyImage->generateFromHtml(
            $this->templating->render(
                'spot/card.html.twig',
                array(
                    'spot'  => $spot,
                    'urlRosaceImage' => $urlRosaceImage,
                    'urlMareeImage' => $this->container->getParameter('maree_image_directory').DIRECTORY_SEPARATOR,
                )
            ),
            $imagePath
        );
        */
    }

    private function removeFile($filesystem, $pathFile) {
        if ($filesystem->exists($pathFile)) {
            $filesystem->remove($pathFile);
        }
    }
}