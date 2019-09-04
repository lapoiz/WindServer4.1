<?php

namespace App\Controller;


use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController  extends AbstractController
{

    /**
     * @Route("/" , name="home")
     * @return Response
     */
    public function index(SpotRepository $repository) : Response
    {
        $spots = $repository->findAll();
        return $this->render('pages/Home.html.twig',[
            "spots" => $spots,
            "urlImage" => $this->getParameter('snappy_directory')
        ]);
    }
}