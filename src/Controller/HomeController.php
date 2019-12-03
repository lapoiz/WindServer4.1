<?php

namespace App\Controller;


use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController  extends AbstractController
{

    /**
     * @Route("/" , name="home")
     * @return Response
     */
    public function index(SpotRepository $repository, DisplayObject $displayObject) : Response
    {
        $spots = $repository->findAll();
        return $this->render('pages/Home.html.twig',[
            "spots" => $spots,
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            "urlImage" => $this->getParameter('card_image_directory')
        ]);
    }
}