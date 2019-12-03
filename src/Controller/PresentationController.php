<?php

namespace App\Controller;


use App\Repository\SpotRepository;
use App\Service\DisplayObject;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PresentationController extends AbstractController
{
    /**
     * @Route("/presentation", name="presentation.index")
     * @return Response
     */
    public function index(SpotRepository $repository, DisplayObject $displayObject) : Response
    {
        $spots = $repository->findAll();
        return $this->render("pages/Presentation.html.twig", [
            "regionsNavBar" => $displayObject->regionsForNavBar($spots),
            'current_menu' => 'presentation'
        ]);
    }
}