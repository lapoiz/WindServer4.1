<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PresentationController extends AbstractController
{
    /**
     * @Route("/presentation", name="presentation.index")
     * @return Response
     */
    public function index() : Response
    {
        return $this->render("pages/Presentation.html.twig", [
            'current_menu' => 'presentation'
        ]);
    }
}