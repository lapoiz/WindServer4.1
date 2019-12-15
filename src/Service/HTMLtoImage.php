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

        $immagePath = $this->cardImageDirectoryKernel.DIRECTORY_SEPARATOR.'card.'.$spot->getId().'.jpg';
        $urlRosaceImage = $this->container->getParameter('rosace_directory').DIRECTORY_SEPARATOR;

        $filesystem = new Filesystem();

        if ($filesystem->exists($immagePath)) {
            $filesystem->remove($immagePath);
        }
        $snappyImage = $this->container->get('knp_snappy.image');
        $snappyImage->setOption('quality', 100);
        $snappyImage->setOption('javascript-delay', 1000);
        //$snappyImage->setOption('dpi', 900);
        $snappyImage->setOption('no-stop-slow-scripts', true);

        $snappyImage->generateFromHtml(
            $this->templating->render(
                'spot/card.html.twig',
                array(
                    'spot'  => $spot,
                    'urlRosaceImage' => $urlRosaceImage,
                    'urlMareeImage' => $this->container->getParameter('maree_image_directory').DIRECTORY_SEPARATOR,
                )
            ),
            $immagePath
        );

    }
}