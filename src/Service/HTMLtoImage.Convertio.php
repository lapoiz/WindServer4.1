<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 13/09/2019
 * Time: 20:35
 */

namespace App\Service;


use App\Entity\Spot;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HTMLtoImageConertio
{
    private $convertioKey;
    private $htmlImageDirectoryKernel;
    private $router;

    public function __construct(UrlGeneratorInterface $router, $convertioKey, $cardImageDirectoryKernel)
    {
        $this->router = $router;
        $this->convertioKey = $convertioKey;
        $this->htmlImageDirectoryKernel = $cardImageDirectoryKernel;
    }

    public function createImageCard(Spot $spot) {

        $immagePath = $this->htmlImageDirectoryKernel.DIRECTORY_SEPARATOR.'card.'.$spot->getId().'.jpg';
        $urlCardPage = $this->router->generate('admin.spot.show.card',
            array(
                'id' => $spot->getId()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        if (strpos($urlCardPage,'localhost')) {
            // testing mode ... pas super propre ...
            // car converti appel cette URL => accessible par l'exterieur
            $urlCardPage = 'http://www.lapoiz.com/wind/public/spot/card/1';
        }

        $API = new Convertio($this->convertioKey);

        $API->settings(array('api_protocol' => 'http', 'http_timeout' => 10));
        $API->startFromURL($urlCardPage, 'jpg')
        ->wait()->wait()->wait()
        ->download($immagePath)
        ->delete();
    }
}