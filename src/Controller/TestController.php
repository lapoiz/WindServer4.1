<?php

namespace App\Controller;


use App\Repository\SpotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController  extends AbstractController
{

    /**
     * @Route("/test" , name="test")
     * @return Response
     */
    public function test(SpotRepository $repository) : Response
    {
        $spots = $repository->findAll();
        return $this->render('tests/test-meteo.html.twig',[
            "spots" => $spots,
            "urlImage" => $this->getParameter('snappy_directory').DIRECTORY_SEPARATOR
        ]);
    }
}