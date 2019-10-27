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
        $this->container->get('knp_snappy.image')->generateFromHtml(
            $this->templating->render(
                'spot/card.html.twig',
                array(
                    'spot'  => $spot,
                    'urlRosaceImage' => $urlRosaceImage
                )
            ),
            $immagePath
        );

    }
}