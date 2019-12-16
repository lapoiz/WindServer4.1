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

        imagecopyresampled($emptyImage, $rosaceImage,5,5,0,0,120, 120,imagesx($rosaceImage), imagesy($rosaceImage));

        if ($spot->getURLMaree() != null && !empty($spot->getURLMaree())) {
            $mareeImage = imagecreatefromjpeg($urlMareeImage);
            imagecopyresampled($emptyImage,$mareeImage,5,360,0,0,120, 120,imagesx($mareeImage),imagesy($mareeImage));
        }


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