<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 13/04/2019
 * Time: 23:43
 */

namespace App\Controller\admin;


use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use App\Service\HTMLtoImage;
use Convertio\Convertio;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminTestController extends AbstractController
{
    /**
    * @var SpotRepository
    */
    private $repository;
    var $manager;

    public function __construct(SpotRepository $repository, ObjectManager $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/test", name="admin_test")
     * @param Request $request
     * @return
     */
    public function testAction(HTMLtoImage $hTMLtoImage, DisplayObject $displayObject)
    {
        $spot = $this->repository->findFirst();
        $hTMLtoImage->createImageCard($spot);

        $spots = $this->repository->findAll();
        return $this->render('admin/test/index.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'urlImage' => $this->getParameter('rosace_directory').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/admin/test2", name="admin_test2")
     * @param Request $request
     * @return
     */
    public function test2Action(Request $request, DisplayObject $displayObject)
    {
        $spot = $this->repository->findFirst();

        $key=$this->getParameter('convertio_key');
        $immagePath = $this->getParameter('convertio_directory_kernel').DIRECTORY_SEPARATOR.'card.'.$spot->getId().'.jpg';
        $urlCardPage = $this->generateUrl('admin.spot.show.card',
            array(
                'id' => $spot->getId()
            ),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $urlCardPage = 'http://www.lapoiz.com/wind/public/spot/card/1';

        $API = new Convertio($key);

        $API->settings(array('api_protocol' => 'http', 'http_timeout' => 10));

/*        $API->startFromURL('http://google.com/', 'png')   // Convert (Render) HTML Page to PNG
        ->wait()                                          // Wait for conversion finish
        ->download($immagePath)                        // Download Result To Local File
        ->delete();
        */
        $API->startFromURL($urlCardPage, 'jpg')   // Convert (Render) HTML Page to PNG
        ->wait()                                          // Wait for conversion finish
        ->download($immagePath)                        // Download Result To Local File
        ->delete();

        $spots = $this->repository->findAll();
        return $this->render('admin/test/index.html.twig', [
            'spot' => $spot,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'urlImage' => $this->getParameter('rosace_directory').DIRECTORY_SEPARATOR,
        ]);
    }

}