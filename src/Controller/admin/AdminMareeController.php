<?php
/**
 * Created by PhpStorm.
 * User: dpoizat
 * Date: 29/10/2019
 * Time: 22:17
 */

namespace App\Controller\admin;


use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class AdminMareeController extends AbstractController
{

    /**
     * @var SpotRepository
     */
    private $repository;

    /**
     * AdminMareeController constructor.
     * @param SpotRepository $repository
     */
    public function __construct(SpotRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @Route("admin/spots/maree/toimage" , name="admin.spots.maree.to.image")
     * @return Response
     */
    public function mareeToImage(SpotRepository $repository) : Response
    {
        $spots = $repository->findAll();
        $urlHighchartsExportServer = $this->getParameter('url_highcharts-export-server');
        return $this->render('admin/spot/mareesToImage.html.twig',[
            "spots" => $spots,
            "urlHighchartsExportServer" => $urlHighchartsExportServer
        ]);
    }

    /**
         * @Route("admin/ajax/spots/maree/url/image" , name="admin.ajax.spots.maree.url.image")
     * @return Response
     */
    public function mareeURLToImage(Request $request) : Response
    {

        $reponse = new JsonResponse();
        $reponse->headers->set('Content-Type', 'application/json');
        $reponse->setData(array('result' => 'KO','message'=>'par defaut'));

        //if($request->isXmlHttpRequest()) {
            $urlImage = $request->request->get('urlImage');
            $spotId = $request->request->get('spotId');
            $save_to = $this->getParameter('maree_directory_kernel') . DIRECTORY_SEPARATOR . $spotId . '.png';
            if ($urlImage) {
                $content = file_get_contents($urlImage);
                file_put_contents($save_to, $content);

                $reponse->setData(array('result' => 'OK'));
            }
        //    $reponse->setData(array('result' => 'KO','message'=>'no Ajax request.' ));
        //}
        return $reponse;
    }


    /**
     * @Route("highcharts-export-server" , name="highcharts-export-server")
     * @return Response
     */
    public function highchartsExportServer(Request $request) : Response
    {
        $reponse = new JsonResponse();
        $reponse->headers->set('Content-Type', 'application/json');
        $reponse->setData(array('result' => 'KO','message'=>'par defaut'));

        //if($request->isXmlHttpRequest()) {
        $chartJson = $request->request->get('options');

        $process = new Process(['highcharts-export-server', '-instr',$chartJson]);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $reponse->setData(array('result' => 'OK'));

        return $reponse;
    }
}